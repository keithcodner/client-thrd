<?php

namespace App\Http\Controllers;

use App\Enums\OperationEnum;
use App\Http\Requests;
use App\Models\Image;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Asset\Image as CloudinaryImage;
use Cloudinary\Transformation\AspectRatio;
use Cloudinary\Transformation\Background;
use Cloudinary\Transformation\Resize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function fill(Request $request)
    {
        //Rembember to change .env back to local storage after testing with cloudinary!!!!!!
        $operation = OperationEnum::GENERATIVE_FILL;
        $this->checkCredits($operation);

        $request->validate([
            'image' => 'required|image|max:10240',
            'aspectRatio' => 'required|string',
        ]);

        $image = $request->file('image');
        $aspectRatio = $request->input('aspectRatio');

        $aspectRatioMethod = $this->getAspectRatioMethod($aspectRatio);

        //Upload the original image to Cloudinary
        $originalPublicId = $image->store('uploads');

        // Get the dimensions of the original image
        $imageSize = getImageSize($image);
        $originalWidth = $imageSize[0];
        $originalHeight = $imageSize[1];

        // Determine the padding dimensions based on the aspect ratio
        $pad = Resize::pad();
        if(in_array($aspectRatio, ['16:9', '4:3'])) {
            $pad->height($originalHeight);
        } else {
            $pad->width($originalWidth);
        }

        // Generate the new image with the specified aspect ratio and generative fill background
        $generatedImg = (new CloudinaryImage($originalPublicId))->resize(
            $pad
            ->aspectRatio(AspectRatio::{$aspectRatioMethod}())
            ->background(Background::generativeFill())
        );

        // Get the URL of the generated image
        $transformedImageURL = $generatedImg->toUrl();

        $uploadResult = (new UploadApi())->upload($transformedImageURL, [
            'folder' => 'transformed/gen_fill', // target folder in Cloudinary
            //'public_id' => $image->getClientOriginalName(), // optional: use original file name as public ID
        ]);

        // Get the URL of the uploaded image
        $uploadedImageURL = $uploadResult['secure_url'];

        // Optionally, you can also get the public ID of the uploaded image
        $transformedPublicId = 'uploads/' . $uploadResult['public_id'];

        // Save the image operation details to the database
        $this->saveImageOperation(
            $originalPublicId,
            Storage::url($originalPublicId),
            $transformedPublicId,
            $uploadedImageURL,
            OperationEnum::GENERATIVE_FILL->value,
            ['aspect_ratio' => $aspectRatio]
        );

        $this->deductCredits($operation);

        return response()->json([
            'message' => 'Image uploaded and transformed successfully',
            'transformed_url' => $transformedImageURL,
            'credits' => request()->user()->credits,
            'aspectRatio' => $aspectRatio,
        ]);

    }

    public function getLatestOperations(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $operations = Image::where( 'user_id',  $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Add operation credits information
        $operations->getCollection()->transform(function ($operation){
            $operationType = $operation->operation_type;
            $enumType = match ($operationType) {
                'generative_fill' => OperationEnum::GENERATIVE_FILL,
                'restore' => OperationEnum::RESTORE,
                'recolour' => OperationEnum::RECOLOUR,
                'remove_object' => OperationEnum::REMOVE_OBJECTS,
                default => null,
            };

            $operation->credits_user = $enumType ? $enumType->credits() : 0;
            return $operation;
        });

        return response()->json([
            'operations' => $operations->items(),
            'pagination' => [
                'current_page' => $operations->currentPage(),
                'last_page' => $operations->lastPage(),
                'per_page' => $operations->perPage(),
                'total' => $operations->total(),
                'has_more_pages' => $operations->hasMorePages(),
            ],
        ]);
    }
    public function getOperation($id)
    {
        $user = Auth::user();
        $operation = Image::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Add operation credits information
        $operationType = $operation->operation_type;
        $enumType = match ($operationType) {
            'generative_fill' => OperationEnum::GENERATIVE_FILL,
            'restore' => OperationEnum::RESTORE,
            'recolour' => OperationEnum::RECOLOUR,
            'remove_object' => OperationEnum::REMOVE_OBJECTS,
            default => null,
        };
        $operation->credits_user = $enumType ? $enumType->credits() : 0;

        return response()->json([
            'operation'=> $operation,
        ]);
    }

    public function deleteOperation($id)
    {
        $user = Auth::user();
        $operation = Image::where( 'user_id',  $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Delete images from Cloudinary if they exist
        try {
            if ($operation->original_image_public_id) {
                (new AdminApi())->deleteAssets(publicIds: [
                    $operation->original_image_public_id
                ]);
            }

            if ($operation->generated_image_public_id) {
                (new AdminApi())->deleteAssets(publicIds: [
                    $operation->generated_image_public_id
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but continue with deleting the database record
            Log::error('Failed to delete images from Cloudinary: ' . $e->getMessage());
        }

        $operation->delete();

        return response()->json([
            'message' => 'Operation and associated images deleted successfully',
        ]);
    }

    private function saveImageOperation(string $originalPublicId, string $originalImageURL, string $generatedPublicId, string $generatedImageURL, string $operationType, array $operationMetadata)
    {
        Image::create([
            'user_id' => Auth::id(),
            'original_image_public_id' => $originalPublicId,
            'original_image' => $originalImageURL,
            'generated_image_public_id' => $generatedPublicId,
            'generated_image' => $generatedImageURL,
            'operation_type' => $operationType,
            'operation_metadata' => $operationMetadata,
        ]);
    }

    private function checkCredits(OperationEnum $operation)
    {
        $user = request()->user();
        $requiredCredits = $operation->credits();

        if($user->credits < $requiredCredits) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
                'message' => 'Insufficient credits. This operation requires ' . $requiredCredits . ' credits. You currently have ' . $user->credits . ' credits.',
                'credits' => $user->credits,
            ], 403));
        }
    }

    private function deductCredits(OperationEnum $operation)
    {
        $user = request()->user();
        $requiredCredits = $operation->credits();
        $user->credits -= $requiredCredits;
        $user->save();
    }

    private function getAspectRatioMethod(string $ratio): string
    {
        return match($ratio){
            '1:1' => 'ar1x1',
            '16:9'=> 'ar16x9',
            '4:3' => 'ar4x3',
            default => 'ar1x1'
        };
    }
}
