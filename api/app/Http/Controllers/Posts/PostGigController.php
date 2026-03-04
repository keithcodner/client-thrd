<?php
namespace App\Http\Controllers\Posts;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Posts\JobPost;
use App\Models\Posts\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Core\FileTemporary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Core\FilePostStored;
use App\Models\PaymentTransactions\PaymentTransactionProduct;
use App\Http\Controllers\E_Commerce\PaymentProviders\StripeController;
use App\Jobs\GenerateCompanyInfoJob;

class PostGigController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth']);
    }
    /**
     * Check if Stripe webhook listener is running
     * Delegates to StripeController for consistency
     */
    private function checkStripeWebhookHealth()
    {
        try {
            $stripeController = app(StripeController::class);
            $response = $stripeController->checkWebhookStatus();
            $data = $response->getData(true);
            return $data['healthy'] ?? false;
        } catch (\Exception $e) {
            Log::error('Failed to check Stripe webhook health: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment the clicks count for a job post (when Apply button is clicked)
    */
    public function recordClick(Request $request, $id)
    {
        try {
            Log::info('Recording click for job post', ['job_id' => $id]);
            $job = JobPost::findOrFail($id);
            
            // Handle NULL values explicitly
            if ($job->clicks === null) {
                $job->clicks = 0;
            }
            $job->clicks = $job->clicks + 1;
            $job->save();
            
            Log::info('Click recorded successfully', ['job_id' => $id, 'clicks' => $job->clicks]);
            return response()->json(['success' => true, 'clicks' => $job->clicks]);
        } catch (\Exception $e) {
            Log::error('Failed to record click', ['job_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Increment the views count for a job post (when description is expanded)
     */
    public function recordView(Request $request, $id)
    {
        try {
            Log::info('Recording view for job post', ['job_id' => $id]);
            $job = JobPost::findOrFail($id);
            
            // Handle NULL values explicitly
            if ($job->views === null) {
                $job->views = 0;
            }
            $job->views = $job->views + 1;
            $job->save();
            
            Log::info('View recorded successfully', ['job_id' => $id, 'views' => $job->views]);
            return response()->json(['success' => true, 'views' => $job->views]);
        } catch (\Exception $e) {
            Log::error('Failed to record view', ['job_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    

     /**
     * Remove a draft job post (set status to UNPAID_TO_BE_PAID_DRAFT_REMOVED)
     */
    public function removeDraft(Request $request, $id)
    {
        $job = JobPost::where('id', $id)->where('status', 'DRAFT')->first();
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Draft post not found or not in draft status.'
            ], 404);
        }
        $job->status = 'UNPAID_TO_BE_PAID_DRAFT_REMOVED';
        $job->save();
        return response()->json([
            'success' => true,
            'message' => 'Draft post removed successfully.',
            'job' => $job
        ]);
    }

    public function index(Request $request)
    {
        $job = null;
        $existingImage = null;
        
        if ($request->has('view_post')) {
            $job = JobPost::find($request->view_post);
            
            // Get existing image if it exists
            if ($job) {
                $imageFile = FilePostStored::where('post_id', $job->id)
                    ->where('type', 'job_post')
                    ->where('status', 'active')
                    ->first();
                
                if ($imageFile) {
                    $existingImage = asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
                }
            }
            
            return Inertia::render('PostGig/PostGig', [
                'job' => $job,
                'existingImage' => $existingImage,
                'readOnly' => true,
                'featureDetails' => PaymentTransactionProduct::where('category_id', 39)->get(),
            ]);
        }

        if ($request->has('draft_id')) {
            // Use draft_id (alphanumeric string) to find the job
            $job = JobPost::where('draft_id', $request->query('draft_id'))->first();
            
            // Get existing image if it exists
            if ($job) {
                $imageFile = FilePostStored::where('post_id', $job->id)
                    ->where('type', 'job_post')
                    ->where('status', 'active')
                    ->first();
                
                if ($imageFile) {
                    $existingImage = asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
                }
            }
            
            return Inertia::render('PostGig/PostGig', [
                'job' => $job,
                'existingImage' => $existingImage,
                'readOnly' => false, // Allow editing drafts
                'featureDetails' => PaymentTransactionProduct::where('category_id', 39)->get(),
            ]);
        }

        // Default: create new post
        return Inertia::render('PostGig/PostGig', [
            'job' => $job,
            'existingImage' => $existingImage,
            'readOnly' => false,
            'featureDetails' => PaymentTransactionProduct::where('category_id', 39)->get(),
        ]);
    }

    public function store(Request $request)
    {
        // ✅ CRITICAL: Verify email before allowing post creation
        if (!Auth::user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'error' => 'email_not_verified',
                'message' => 'You must verify your email address before creating posts.',
            ], 403);
        }

        // ✅ CRITICAL: Enforce mutual exclusivity - only ONE sticky option allowed
        $stickyCount = 0;
        if ($this->toBool($request->input('sticky24h'))) $stickyCount++;
        if ($this->toBool($request->input('sticky1Week'))) $stickyCount++;
        if ($this->toBool($request->input('sticky1Month'))) $stickyCount++;
        
        if ($stickyCount > 1) {
            Log::warning('Multiple sticky flags detected - potential fraud attempt', [
                'user_id' => Auth::id(),
                'sticky24h' => $request->input('sticky24h'),
                'sticky1Week' => $request->input('sticky1Week'),
                'sticky1Month' => $request->input('sticky1Month'),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'invalid_features',
                'message' => 'Only one sticky option can be selected at a time.',
            ], 400);
        }

        $saveType = $request->input('save_type'); // 'draft' | 'post'
        $draftId  = $request->input('draft_id'); // Client-generated unique ID

        // ✅ CRITICAL: Check webhook health for payment posts (not drafts)
        if ($saveType === 'post') {
            if (!$this->checkStripeWebhookHealth()) {
                Log::error('PostGig store blocked - Stripe webhook not running', [
                    'user_id' => Auth::id(),
                    'draft_id' => $draftId
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'payment_system_unavailable',
                    'message' => 'Payment system is currently unavailable. Please save as draft and try again later, or contact support.',
                ], 503);
            }
        }

        // DUPLICATE SUBMISSION PROTECTION
        // Check if a record with this draft_id already exists
        if ($draftId) {
            $existingJob = JobPost::where('draft_id', $draftId)->first();
            if ($existingJob) {
                Log::info('Duplicate submission detected for draft_id: ' . $draftId);
                return response()->json([
                    'success' => true,
                    'job' => $existingJob,
                    'job_post_id' => $existingJob->id,
                    'message' => 'Job post already exists (duplicate submission prevented)',
                ]);
            }
        } else {
            // If no draft_id provided, generate one server-side as fallback
            $draftId = uniqid('draft_', true);
            Log::warning('No draft_id provided by client, generated server-side: ' . $draftId);
        }

        $data = $request->only([
            'title','job_description','budget','location','company_name'
            // add other fields you allow
        ]);

        // Process image BEFORE creating job record - if this fails, no job record is created
        $hasImage = $request->filled('imageFolder');
        $imageFolder = $request->input('imageFolder');
        $imageProcessingData = null;

        if ($hasImage && $imageFolder) {
            try {
                // Debug: Log what we're looking for and what exists
                Log::info('Looking for temp image with folder: ' . $imageFolder);
                $allTempImages = FileTemporary::all(['foldername', 'filename', 'created_at']);
                Log::info('All temp images in database: ' . $allTempImages->toJson());
                
                $tempImage = FileTemporary::where('foldername', $imageFolder)->first();
                
                if (!$tempImage) {
                    Log::error('Temp image not found for folder: ' . $imageFolder);
                    return response()->json(['status' => 'error', 'message' => 'Temp image not found'], 404);
                }

                $filename = $tempImage->filename;
                $foldername = $tempImage->foldername;
                $userFolder = 'user_' . (Auth::id() ?? 'guest');
                $sourceFile = 'temp_data_img/' . $foldername . '/' . $filename;

                // Check if source file exists (on public disk)
                if (!Storage::disk('public')->exists($sourceFile)) {
                    Log::error('Source file does not exist: ' . $sourceFile);
                    //return response()->json(['status' => 'error', 'message' => 'Source file not found'], 404);
                }

                // Store all the data we'll need after job creation
                $imageProcessingData = [
                    'tempImage' => $tempImage,
                    'filename' => $filename,
                    'foldername' => $foldername,
                    'userFolder' => $userFolder,
                    'sourceFile' => $sourceFile
                ];

            } catch (\Throwable $e) {
                Log::error('Image validation failed: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Image validation failed: ' . $e->getMessage()], 500);
            }
        }

        // Validate request
        $validated = $request->validate([
                'gigTitle' => 'nullable|string|max:255',
                'gigDescription' => 'nullable|string',
                'budget' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:10',
                'locationRestriction' => 'nullable|string|max:255',
                'locationCountry' => 'nullable|string|max:255',
                'locationState' => 'nullable|string|max:255',
                'locationCity' => 'nullable|string|max:255',
                'companyLogo' => 'nullable|file|image|max:2048',
                //'brandColor' => 'nullable|boolean',
                'salaryMin' => 'nullable|numeric|min:0',
                'salaryMax' => 'nullable|numeric|min:0',
                'benefits' => 'nullable|json',
                'applyUrl' => 'nullable|url',
                'applyEmail' => 'nullable|email',
                'companyTwitter' => 'nullable|string|max:255',
                'companyEmail' => 'nullable|email',
                'companyName' => 'nullable|string|max:255',
                'invoiceEmail' => 'nullable|email',
                'invoiceAddress' => 'nullable|string',
                'invoiceNotes' => 'nullable|string',
                'feedback' => 'nullable|string',
            ]);

            // Upload logo if present
            $logoPath = null;
            if ($request->hasFile('companyLogo')) {
                $logoPath = $request->file('companyLogo')->store('logos', 'public');
            }

            // Process location data
            $locationCountry = $request->input('locationCountry');
            $locationState = $request->input('locationState');
            $locationCity = $request->input('locationCity');
            $locationRestriction = $request->input('locationRestriction');
            
            // Format location_restriction based on country selection
            if ($locationRestriction === 'Specific Country') {
                if ($locationCountry) {
                    if (($locationCountry === 'Canada' || $locationCountry === 'US')) {
                        if ($locationCity && $locationState) {
                            // Format: "Toronto, ON, Canada" or "San Antonio, TX, USA"
                            $countryCode = $locationCountry === 'Canada' ? 'Canada' : 'USA';
                            $locationRestriction = $locationCity . ', ' . $locationState . ', ' . $countryCode;
                        } elseif ($locationState) {
                            // Only state provided: "Ontario, Canada" or "Texas, USA"
                            $countryCode = $locationCountry === 'Canada' ? 'Canada' : 'USA';
                            $locationRestriction = $locationState . ', ' . $countryCode;
                        } else {
                            // Only country provided
                            $locationRestriction = $locationCountry === 'Canada' ? 'Canada' : 'United States';
                        }
                    } else {
                        // For other countries - just use the country name
                        $locationRestriction = $locationCountry;
                    }
                }
                // If no country selected, leave as "Specific Country"
            }

            $job = JobPost::create([
                'author_id' => Auth::id() ?? 0,
                'title' => $request->input('gigTitle'),
                'draft_id' => uniqid(),
                'slug' => Str::slug($request->input('gigTitle')) . '-' . uniqid(),
                'job_description' => $request->input('gigDescription'),
                'budget' => $request->input('budget'),
                'currency' => $request->input('currency'),
                'payment_frequency' => $request->input('paymentFrequency'),
                'skills' => json_decode($request->input('skills') ?? '[]', true),
                'location_type' => $request->input('locationType'),
                'employer_type' => $request->input('employmentType'),
                'primary_tag' => Str::limit($request->input('primaryTag') ?? '', 255),
                'secondary_tags' => json_decode($request->input('tags') ?? '[]', true),
                'location_restriction' => $locationRestriction,
                'location_country' => $locationCountry,
                'location_state_province' => $locationState,
                'location_city' => $locationCity,
                'company_logo' => $logoPath,
                'brand_color' => $this->toBool($request->input('brandColor')),
                'salary_min' => $request->input('salaryMin'),
                'salary_max' => $request->input('salaryMax'),
                'benefits' => json_decode($request->input('benefits') ?? '[]', true),
                'apply_url' => $request->input('applyUrl'),
                'apply_email_address' => $request->input('applyEmail'),
                'company_twitter' => $request->input('companyTwitter'),
                'company_email' => $request->input('companyEmail'),
                'company_name' => $request->input('companyName'),
                'invoice_email' => $request->input('invoiceEmail'),
                'invoice_address' => $request->input('invoiceAddress'),
                'invoice_notes_po_box_number' => $request->input('invoiceNotes'),
                'feedback_box' => $request->input('feedback'),
                //'pay_later' => $this->toBool($request->input('payLater')),
                'pay_later' => 0,
                'status' => 'UNPAID_TO_BE_PAID_DRAFT', // Changed from UNPAID - saves as draft until payment confirmed

                'expires_at' => Carbon::now()->addDays(30), // Set expiration to 30 days from now

                'show_company_logo' => $this->toBool($request->input('showCompanyLogo')),
                'email_blast_job' => $this->toBool($request->input('emailBlast')),
                'create_qr_code' => $this->toBool($request->input('qrCodeShortLink')),
                'highlight_post' => $this->toBool($request->input('highlightPost')),
                'sticky_note_24_hour' => $this->toBool($request->input('sticky24h')),
                'sticky_note_week' => $this->toBool($request->input('sticky1Week')),
                'sticky_note_month' => $this->toBool($request->input('sticky1Month')),
                'geo_lock_post' => $this->toBool($request->input('geoLock')),
                'highlight_company_with_color' => $this->toBool($request->input('companyLogoHighlight')),
                'highlight_company' => $this->toBool($request->input('highlightCompany')),

                
            ]);

            // Now process the image after job creation (we already validated it exists)
            if ($imageProcessingData) {
                try {
                    $tempImage = $imageProcessingData['tempImage'];
                    $filename = $imageProcessingData['filename'];
                    $foldername = $imageProcessingData['foldername'];
                    $userFolder = $imageProcessingData['userFolder'];
                    $sourceFile = $imageProcessingData['sourceFile'];
                    
                    $targetFolder = 'store_data/posts/draft/' . $userFolder . '/' . $foldername;
                    $targetFile = $targetFolder . '/' . $filename;

                    // Create target directory and move file on public disk
                    Storage::disk('public')->makeDirectory($targetFolder);
                    
                    if (Storage::disk('public')->move($sourceFile, $targetFile)) {
                        // Store image record
                        FilePostStored::create([
                            'post_id' => $job->id,
                            'file_store_an_id' => $job->id,
                            'filename' => $filename,
                            'foldername' => $userFolder . '/' . $foldername,
                            'status' => 'active',
                            'type' => 'job_post',
                            'order' => 'first',
                        ]);

                        // Delete temp record and directory
                        $tempImage->delete();
                        Storage::disk('public')->deleteDirectory('temp_data_img/' . $foldername);
                    } else {
                        Log::error('Failed to move file after job creation');
                        // Don't fail the job creation, just log the error
                    }
                } catch (\Throwable $e) {
                    Log::error('Image processing failed after job creation: ' . $e->getMessage());
                    // Don't fail the job creation, just log the error
                }
            }

            // Check if company info exists and queue generation if needed (silently)
            $this->checkAndGenerateCompanyInfo($request->input('companyName'));

            
        return response()->json([
            'success' => true,
            'job' => $job,
            'job_post_id' => $job->id,
        ]);
    }

    // Update an already paid (COMMITTED) post without changing its paid status
    public function updatePaid(Request $request, $id)
    {
        $job = JobPost::findOrFail($id);
        if ($job->status !== 'COMMITTED') {
            return response()->json(['error' => 'Post not committed'], 422);
        }

        $data = $request->only([
            'title','job_description','budget','location','company_name'
            // add other fields you allow
        ]);

        $job->fill($data);
        // Keep status committed
        $job->status = 'COMMITTED';
        $job->save();

        return response()->json([
            'success' => true,
            'job' => $job,
        ]);
    }

    public function draft(Request $request)
    {
        // ✅ CRITICAL: Verify email before allowing draft creation
        if (!Auth::user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'error' => 'email_not_verified',
                'message' => 'You must verify your email address before creating drafts.',
            ], 403);
        }

        // ✅ CRITICAL: Enforce mutual exclusivity - only ONE sticky option allowed
        $stickyCount = 0;
        if ($this->toBool($request->input('sticky24h'))) $stickyCount++;
        if ($this->toBool($request->input('sticky1Week'))) $stickyCount++;
        if ($this->toBool($request->input('sticky1Month'))) $stickyCount++;
        
        if ($stickyCount > 1) {
            Log::warning('Multiple sticky flags in draft - potential fraud attempt', [
                'user_id' => Auth::id(),
                'sticky24h' => $request->input('sticky24h'),
                'sticky1Week' => $request->input('sticky1Week'),
                'sticky1Month' => $request->input('sticky1Month'),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'invalid_features',
                'message' => 'Only one sticky option can be selected at a time.',
            ], 400);
        }

        // Check if we're updating an existing draft/post
        $postId = $request->input('post_id');
        $existingJob = null;
        
        if ($postId) {
            // Updating existing draft
            $existingJob = JobPost::where('id', $postId)
                ->where('author_id', Auth::id())
                ->first();
            
            if ($existingJob) {
                Log::info('Updating existing draft with post_id: ' . $postId);
            } else {
                Log::warning('Post ID provided but not found or not owned by user: ' . $postId);
            }
        }
        
        // Get client-provided draft_id for duplicate protection (only for new drafts)
        $draftId = $request->input('draft_id');

        // DUPLICATE SUBMISSION PROTECTION (only for new drafts)
        if (!$existingJob && $draftId) {
            $duplicateJob = JobPost::where('draft_id', $draftId)->first();
            if ($duplicateJob) {
                Log::info('Duplicate draft submission detected for draft_id: ' . $draftId);
                return response()->json([
                    'success' => true,
                    'job' => $duplicateJob,
                    'message' => 'Draft already exists (duplicate submission prevented)',
                ]);
            }
        } elseif (!$existingJob && !$draftId) {
            // If no draft_id provided for new draft, generate one server-side as fallback
            $draftId = uniqid('draft_', true);
            Log::warning('No draft_id provided by client for new draft, generated server-side: ' . $draftId);
        }

        $validated = $request->validate([
            'gigTitle' => 'nullable|string|max:255',
            'gigDescription' => 'nullable|string',
            'budget' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'locationRestriction' => 'nullable|string|max:255',
            'locationCountry' => 'nullable|string|max:255',
            'locationState' => 'nullable|string|max:255',
            'locationCity' => 'nullable|string|max:255',
            'companyLogo' => 'nullable|file|image|max:2048',
            //'brandColor' => 'nullable|boolean',
            'salaryMin' => 'nullable|numeric|min:0',
            'salaryMax' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|json',
            'applyUrl' => 'nullable|url',
            'applyEmail' => 'nullable|email',
            'companyTwitter' => 'nullable|string|max:255',
            'companyEmail' => 'nullable|email',
            'companyName' => 'nullable|string|max:255',
            'invoiceEmail' => 'nullable|email',
            'invoiceAddress' => 'nullable|string',
            'invoiceNotes' => 'nullable|string',
            'feedback' => 'nullable|string',
        ]);

        // Process image BEFORE creating job record - if this fails, no job record is created
        $hasImage = $request->filled('imageFolder');
        $imageFolder = $request->input('imageFolder');
        $imageProcessingData = null;

        if ($hasImage && $imageFolder) {
            try {
                // Debug: Log what we're looking for and what exists
                Log::info('Looking for temp image with folder: ' . $imageFolder);
                $allTempImages = FileTemporary::all(['foldername', 'filename', 'created_at']);
                Log::info('All temp images in database: ' . $allTempImages->toJson());
                
                $tempImage = FileTemporary::where('foldername', $imageFolder)->first();
                
                if (!$tempImage) {
                    Log::error('Temp image not found for folder: ' . $imageFolder);
                    return response()->json(['status' => 'error', 'message' => 'Temp image not found'], 404);
                }

                $filename = $tempImage->filename;
                $foldername = $tempImage->foldername;
                $userFolder = 'user_' . (Auth::id() ?? 'guest');
                $sourceFile = 'temp_data_img/' . $foldername . '/' . $filename;

                // Check if source file exists (on public disk)
                if (!Storage::disk('public')->exists($sourceFile)) {
                    Log::error('Source file does not exist: ' . $sourceFile);
                    //return response()->json(['status' => 'error', 'message' => 'Source file not found'], 404);
                }

                // Store all the data we'll need after job creation
                $imageProcessingData = [
                    'tempImage' => $tempImage,
                    'filename' => $filename,
                    'foldername' => $foldername,
                    'userFolder' => $userFolder,
                    'sourceFile' => $sourceFile
                ];

            } catch (\Throwable $e) {
                Log::error('Image validation failed: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Image validation failed: ' . $e->getMessage()], 500);
            }
        }

        // Upload logo if present
        $logoPath = null;
        if ($request->hasFile('companyLogo')) {
            $logoPath = $request->file('companyLogo')->store('logos', 'public');
        }

        // Process location data
        $locationCountry = $request->input('locationCountry');
        $locationState = $request->input('locationState');
        $locationCity = $request->input('locationCity');
        $locationRestriction = $request->input('locationRestriction');
        
        // Format location_restriction based on country/state/city
        // Frontend now sends formatted value, but keep this as fallback
        if ($locationCountry && ($locationRestriction === 'Specific Country' || empty($locationRestriction))) {
            $parts = [];
            if ($locationCity) $parts[] = $locationCity;
            if ($locationState) $parts[] = $locationState;
            
            // Add country - use USA for United States
            $countryName = ($locationCountry === 'United States') ? 'USA' : $locationCountry;
            $parts[] = $countryName;
            
            $locationRestriction = implode(', ', $parts);
        }

        // Prepare data for create or update
        $jobData = [
            'author_id' => Auth::id() ?? 0,
            'title' => $request->input('gigTitle'),
            'slug' => Str::slug($request->input('gigTitle') ?: 'untitled') . '-' . uniqid(),
            'job_description' => $request->input('gigDescription'),
            'budget' => $request->input('budget'),
            'currency' => $request->input('currency'),
            'payment_frequency' => $request->input('paymentFrequency'),
            'skills' => json_decode($request->input('skills') ?? '[]', true),
            'location_type' => $request->input('locationType'),
            'employer_type' => $request->input('employmentType'),
            'primary_tag' => Str::limit($request->input('primaryTag') ?? '', 255),
            'secondary_tags' => json_decode($request->input('tags') ?? '[]', true),
            'location_restriction' => $locationRestriction,
            'location_country' => $locationCountry,
            'location_state_province' => $locationState,
            'location_city' => $locationCity,
            'brand_color' => $this->toBool($request->input('brandColor')),
            'salary_min' => $request->input('salaryMin'),
            'salary_max' => $request->input('salaryMax'),
            'benefits' => json_decode($request->input('benefits') ?? '[]', true),
            'apply_url' => $request->input('applyUrl'),
            'apply_email_address' => $request->input('applyEmail'),
            'company_twitter' => $request->input('companyTwitter'),
            'company_email' => $request->input('companyEmail'),
            'company_name' => $request->input('companyName'),
            'invoice_email' => $request->input('invoiceEmail'),
            'invoice_address' => $request->input('invoiceAddress'),
            'invoice_notes_po_box_number' => $request->input('invoiceNotes'),
            'feedback_box' => $request->input('feedback'),
            'pay_later' => 0,
            'status' => 'DRAFT',
            'expires_at' => Carbon::now()->addDays(30),
            'free_tier' => $this->toBool($request->input('freeTier')),
            'show_company_logo' => $this->toBool($request->input('showCompanyLogo')),
            'email_blast_job' => $this->toBool($request->input('emailBlast')),
            'create_qr_code' => $this->toBool($request->input('qrCodeShortLink')),
            'highlight_post' => $this->toBool($request->input('highlightPost')),
            'sticky_note_24_hour' => $this->toBool($request->input('sticky24h')),
            'sticky_note_week' => $this->toBool($request->input('sticky1Week')),
            'sticky_note_month' => $this->toBool($request->input('sticky1Month')),
            'geo_lock_post' => $this->toBool($request->input('geoLock')),
            'highlight_company_with_color' => $this->toBool($request->input('companyLogoHighlight')),
            'highlight_company' => $this->toBool($request->input('highlightCompany')),
        ];
        
        // Add company logo if uploaded
        if ($logoPath) {
            $jobData['company_logo'] = $logoPath;
        }
        
        // Update existing draft or create new one
        if ($existingJob) {
            // Update existing draft
            $existingJob->update($jobData);
            $job = $existingJob;
            Log::info('Updated existing draft ID: ' . $job->id);
        } else {
            // Create new draft with draft_id
            $jobData['draft_id'] = $draftId;
            $job = JobPost::create($jobData);
            Log::info('Created new draft with draft_id: ' . $draftId);
        }
            if ($imageProcessingData) {
                try {
                    $tempImage = $imageProcessingData['tempImage'];
                    $filename = $imageProcessingData['filename'];
                    $foldername = $imageProcessingData['foldername'];
                    $userFolder = $imageProcessingData['userFolder'];
                    $sourceFile = $imageProcessingData['sourceFile'];
                    
                    $targetFolder = 'store_data/posts/draft/' . $userFolder . '/' . $foldername;
                    $targetFile = $targetFolder . '/' . $filename;

                    // Create target directory and move file on public disk
                    Storage::disk('public')->makeDirectory($targetFolder);
                    
                    if (Storage::disk('public')->move($sourceFile, $targetFile)) {
                        // Store image record
                        FilePostStored::create([
                            'post_id' => $job->id,
                            'file_store_an_id' => $job->id,
                            'filename' => $filename,
                            'foldername' => $userFolder . '/' . $foldername,
                            'status' => 'active',
                            'type' => 'job_post',
                            'order' => 'first',
                        ]);

                        // Delete temp record and directory
                        $tempImage->delete();
                        Storage::disk('public')->deleteDirectory('temp_data_img/' . $foldername);
                    } else {
                        Log::error('Failed to move file after job creation');
                        // Don't fail the job creation, just log the error
                    }
                } catch (\Throwable $e) {
                    Log::error('Image processing failed after job creation: ' . $e->getMessage());
                    // Don't fail the job creation, just log the error
                }
            }

        // Check if company info exists and queue generation if needed (silently)
        $this->checkAndGenerateCompanyInfo($request->input('companyName'));

        return response()->json([
            'success' => true,
            'job' => $job,
        ]);
    }

    public function storeImage(Request $request)
    {
        if ($request->hasFile('uploadZone')) {
            $file = $request->file('uploadZone');
            $filename = str_replace(' ', '_', $file->getClientOriginalName());
            $folder = uniqid().'-'.now()->timestamp;
            
            // Store to public disk explicitly: storage/app/public/temp_data_img/FOLDER/filename
            $file->storeAs('temp_data_img/'.$folder, $filename, 'public');

            FileTemporary::create([
                'file_temp_an_id' => $request->image_an_id,
                'foldername' => $folder,
                'filename' => $filename,
            ]);

            return response()->json(['folder' => $folder, 'filename' => $filename]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    // Update this method name and route
    public function storeImageForPost(Request $request)
    {
        if ($request->hasFile('uploadZone')) {
            $file = $request->file('uploadZone');
            $filename = str_replace(' ', '_', $file->getClientOriginalName());
            $folder = uniqid().'-'.now()->timestamp;
            
            // Store to public disk explicitly: storage/app/public/temp_data_img/FOLDER/filename
            $file->storeAs('temp_data_img/'.$folder, $filename, 'public');

            FileTemporary::create([
                'file_temp_an_id' => $request->image_an_id,
                'foldername' => $folder,
                'filename' => $filename,
            ]);

            return response()->json(['folder' => $folder, 'filename' => $filename]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function updatePaidAndCommitted(Request $request, $id)
    {
        $job = JobPost::findOrFail($id);
        if ($job->status !== 'COMMITTED') {
            return response()->json(['error' => 'Post not committed'], 422);
        }

        // Process location data
        $locationCountry = $request->input('locationCountry');
        $locationState = $request->input('locationState');
        $locationCity = $request->input('locationCity');
        $locationRestriction = $request->input('locationRestriction');
        
        // Format location_restriction based on country selection
        if ($locationRestriction === 'Specific Country') {
            if ($locationCountry) {
                if (($locationCountry === 'Canada' || $locationCountry === 'US')) {
                    if ($locationCity && $locationState) {
                        // Format: "Toronto, ON, Canada" or "San Antonio, TX, USA"
                        $countryCode = $locationCountry === 'Canada' ? 'Canada' : 'USA';
                        $locationRestriction = $locationCity . ', ' . $locationState . ', ' . $countryCode;
                    } elseif ($locationState) {
                        // Only state provided: "Ontario, Canada" or "Texas, USA"
                        $countryCode = $locationCountry === 'Canada' ? 'Canada' : 'USA';
                        $locationRestriction = $locationState . ', ' . $countryCode;
                    } else {
                        // Only country provided
                        $locationRestriction = $locationCountry === 'Canada' ? 'Canada' : 'United States';
                    }
                } else {
                    // For other countries - just use the country name
                    $locationRestriction = $locationCountry;
                }
            }
            // If no country selected, leave as "Specific Country"
        }

        // Map all editable fields from the request to the model
        $job->title = $request->input('gigTitle');
        $job->job_description = $request->input('gigDescription');
        $job->budget = $request->input('budget');
        $job->currency = $request->input('currency');
        $job->payment_frequency = $request->input('paymentFrequency');
        $job->skills = json_decode($request->input('skills') ?? '[]', true);
        $job->location_type = $request->input('locationType');
        $job->employer_type = $request->input('employmentType');
        $job->primary_tag = $request->input('primaryTag');
        $job->secondary_tags = json_decode($request->input('tags') ?? '[]', true);
        $job->location_restriction = $locationRestriction;
        $job->location_country = $locationCountry;
        $job->location_state_province = $locationState;
        $job->location_city = $locationCity;
        $job->salary_min = $request->input('salaryMin');
        $job->salary_max = $request->input('salaryMax');
        $job->benefits = json_decode($request->input('benefits') ?? '[]', true);
        $job->apply_url = $request->input('applyUrl');
        $job->apply_email_address = $request->input('applyEmail');
        $job->company_twitter = $request->input('companyTwitter');
        $job->company_email = $request->input('companyEmail');
        $job->company_name = $request->input('companyName');
        $job->invoice_email = $request->input('invoiceEmail');
        $job->invoice_address = $request->input('invoiceAddress');
        $job->invoice_notes_po_box_number = $request->input('invoiceNotes');
        $job->feedback_box = $request->input('feedback');

        // Optionally update logo if present
        if ($request->hasFile('companyLogo')) {
            $logoPath = $request->file('companyLogo')->store('logos', 'public');
            $job->company_logo = $logoPath;
        }

        // Ensure status remains COMMITTED and is not overwritten
        $job->status = 'COMMITTED';
        $job->save();

        return response()->json([
            'success' => true,
            'job' => $job,
        ]);
    }

    /**
     * Mark a draft/unpaid job post as COMMITTED (LIVE) after successful payment
     */
    public function markAsCommitted(Request $request, $id)
    {
        $job = JobPost::findOrFail($id);
        
        // Only allow transitioning from DRAFT to COMMITTED
        if ($job->status !== 'DRAFT') {
            return response()->json([
                'error' => 'Can only commit draft posts',
                'current_status' => $job->status
            ], 422);
        }

        $job->status = 'COMMITTED';
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Job post is now live',
            'job' => $job,
        ]);
    }

    private function toBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }

    /**
     * Check if company info exists and queue AI generation if it doesn't
     * Silent operation - users don't need to know about AI generation
     */
    private function checkAndGenerateCompanyInfo($companyName)
    {
        if (empty($companyName)) {
            return false;
        }

        // Check if company info already exists (case-insensitive)
        $existingCompany = CompanyInfo::whereRaw('LOWER(company_name) = ?', [strtolower($companyName)])->first();

        if ($existingCompany) {
            Log::info("Company info already exists for: {$companyName}");
            return true;
        }

        // Company doesn't exist, queue AI generation job (silently for users)
        Log::info("Company info doesn't exist for: {$companyName}, queuing AI generation");
        
        try {
            $githubToken = config('services.github.token');
            $progressKey = 'company_info_generation_' . uniqid();
            
            GenerateCompanyInfoJob::dispatch(
                [$companyName], // Pass as array
                $githubToken,
                $progressKey
            )->onQueue('default');
            
            Log::info("Successfully queued company info generation for: {$companyName}");
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to queue company info generation for {$companyName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check how many free posts the authenticated user has remaining this month
     */
    public function checkFreePostsRemaining(Request $request)
    {
        try {
            $userId = Auth::id();
            //dd($userId);
            
            if ($userId === null) {
                return response()->json([
                    'remaining' => 5,
                    'canPost' => true,
                    'total' => 5,
                    'used' => 0
                ]);
            }
            
            // Get the start and end of the current month
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            // Count free tier posts made this month
            $freePostsCount = JobPost::where('author_id', $userId)
                ->where('free_tier', 1)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            //dd($freePostsCount);    
            
            $remaining = max(0, 5 - $freePostsCount);
            $canPost = $remaining > 0;
            
            return response()->json([
                'remaining' => $remaining,
                'canPost' => $canPost,
                'total' => 5,
                'used' => $freePostsCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to check free posts remaining: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json([
                'remaining' => 5,
                'canPost' => true,
                'total' => 5,
                'used' => 0,
                'error' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * Store a free tier job post (automatically committed, no payment)
     */
    public function storeFreeTier(Request $request)
    {
        // ✅ CRITICAL: Verify email before allowing free tier post creation
        if (!Auth::user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'error' => 'email_not_verified',
                'message' => 'You must verify your email address before creating posts.',
            ], 403);
        }

        $draftId = $request->input('draft_id'); // Client-generated unique ID

        // Check if user has free posts remaining
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $freePostsCount = JobPost::where('author_id', $userId)
            ->where('free_tier', 1)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        
        if ($freePostsCount >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your monthly limit of 5 free posts.',
                'remaining' => 0
            ], 403);
        }

        // DUPLICATE SUBMISSION PROTECTION
        if ($draftId) {
            $existingJob = JobPost::where('draft_id', $draftId)->first();
            if ($existingJob) {
                Log::info('Duplicate free tier submission detected for draft_id: ' . $draftId);
                return response()->json([
                    'success' => true,
                    'job' => $existingJob,
                    'job_post_id' => $existingJob->id,
                    'message' => 'Job post already exists (duplicate submission prevented)',
                    'remaining' => 5 - $freePostsCount
                ]);
            }
        } else {
            // Free tier posts are auto-committed, so no 'draft_' prefix needed
            $draftId = uniqid();
            Log::warning('No draft_id provided by client for free tier, generated server-side: ' . $draftId);
        }

        // Process image BEFORE creating job record - if this fails, no job record is created
        $hasImage = $request->filled('imageFolder');
        $imageFolder = $request->input('imageFolder');
        $imageProcessingData = null;

        if ($hasImage && $imageFolder) {
            try {
                Log::info('Looking for temp image with folder: ' . $imageFolder);
                $tempImage = FileTemporary::where('foldername', $imageFolder)->first();
                
                if (!$tempImage) {
                    Log::error('Temp image not found for folder: ' . $imageFolder);
                    return response()->json(['status' => 'error', 'message' => 'Temp image not found'], 404);
                }

                $filename = $tempImage->filename;
                $foldername = $tempImage->foldername;
                $userFolder = 'user_' . (Auth::id() ?? 'guest');
                $sourceFile = 'temp_data_img/' . $foldername . '/' . $filename;

                if (!Storage::disk('public')->exists($sourceFile)) {
                    Log::error('Source file does not exist: ' . $sourceFile);
                }

                // Store all the data we'll need after job creation
                $imageProcessingData = [
                    'tempImage' => $tempImage,
                    'filename' => $filename,
                    'foldername' => $foldername,
                    'userFolder' => $userFolder,
                    'sourceFile' => $sourceFile
                ];

            } catch (\Throwable $e) {
                Log::error('Image validation failed: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Image validation failed: ' . $e->getMessage()], 500);
            }
        }

        // Validate request
        $validated = $request->validate([
            'gigTitle' => 'nullable|string|max:255',
            'gigDescription' => 'nullable|string',
            'budget' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'locationRestriction' => 'nullable|string|max:255',
            'locationCountry' => 'nullable|string|max:255',
            'locationState' => 'nullable|string|max:255',
            'locationCity' => 'nullable|string|max:255',
            'companyLogo' => 'nullable|file|image|max:2048',
            'salaryMin' => 'nullable|numeric|min:0',
            'salaryMax' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|json',
            'applyUrl' => 'nullable|url',
            'applyEmail' => 'nullable|email',
            'companyTwitter' => 'nullable|string|max:255',
            'companyEmail' => 'nullable|email',
            'companyName' => 'nullable|string|max:255',
            'invoiceEmail' => 'nullable|email',
            'invoiceAddress' => 'nullable|string',
            'invoiceNotes' => 'nullable|string',
            'feedback' => 'nullable|string',
        ]);

        // Upload logo if present
        $logoPath = null;
        if ($request->hasFile('companyLogo')) {
            $logoPath = $request->file('companyLogo')->store('logos', 'public');
        }

        // Process location data
        $locationCountry = $request->input('locationCountry');
        $locationState = $request->input('locationState');
        $locationCity = $request->input('locationCity');
        $locationRestriction = $request->input('locationRestriction');
        
        // Format location_restriction based on country/state/city
        // Frontend now sends formatted value, but keep this as fallback
        if ($locationCountry && ($locationRestriction === 'Specific Country' || empty($locationRestriction))) {
            $parts = [];
            if ($locationCity) $parts[] = $locationCity;
            if ($locationState) $parts[] = $locationState;
            
            // Add country - use USA for United States
            $countryName = ($locationCountry === 'United States') ? 'USA' : $locationCountry;
            $parts[] = $countryName;
            
            $locationRestriction = implode(', ', $parts);
        }

        $job = JobPost::create([
            'author_id' => Auth::id() ?? 0,
            'title' => $request->input('gigTitle'),
            'draft_id' => $draftId,
            'slug' => Str::slug($request->input('gigTitle')) . '-' . uniqid(),
            'job_description' => $request->input('gigDescription'),
            'budget' => $request->input('budget'),
            'currency' => $request->input('currency'),
            'payment_frequency' => $request->input('paymentFrequency'),
            'skills' => json_decode($request->input('skills') ?? '[]', true),
            'location_type' => $request->input('locationType'),
            'employer_type' => $request->input('employmentType'),
            'primary_tag' => Str::limit($request->input('primaryTag') ?? '', 255),
            'secondary_tags' => json_decode($request->input('tags') ?? '[]', true),
            'location_restriction' => $locationRestriction,
            'location_country' => $locationCountry,
            'location_state_province' => $locationState,
            'location_city' => $locationCity,
            'company_logo' => $logoPath,
            'brand_color' => $this->toBool($request->input('brandColor')),
            'salary_min' => $request->input('salaryMin'),
            'salary_max' => $request->input('salaryMax'),
            'benefits' => json_decode($request->input('benefits') ?? '[]', true),
            'apply_url' => $request->input('applyUrl'),
            'apply_email_address' => $request->input('applyEmail'),
            'company_twitter' => $request->input('companyTwitter'),
            'company_email' => $request->input('companyEmail'),
            'company_name' => $request->input('companyName'),
            'invoice_email' => $request->input('invoiceEmail'),
            'invoice_address' => $request->input('invoiceAddress'),
            'invoice_notes_po_box_number' => $request->input('invoiceNotes'),
            'feedback_box' => $request->input('feedback'),
            'pay_later' => 0,
            'status' => 'COMMITTED', // Free tier posts are auto-committed (bypass payment)

            'expires_at' => Carbon::now()->addDays(30),

            // Free tier: all premium features disabled
            'free_tier' => 1,
            'show_company_logo' => 0,
            'email_blast_job' => 0,
            'create_qr_code' => 0,
            'highlight_post' => 0,
            'sticky_note_24_hour' => 0,
            'sticky_note_week' => 0,
            'sticky_note_month' => 0,
            'geo_lock_post' => 0,
            'highlight_company_with_color' => 0,
            'highlight_company' => 0,
        ]);

        // Now process the image after job creation (we already validated it exists)
        if ($imageProcessingData) {
            try {
                $tempImage = $imageProcessingData['tempImage'];
                $filename = $imageProcessingData['filename'];
                $foldername = $imageProcessingData['foldername'];
                $userFolder = $imageProcessingData['userFolder'];
                $sourceFile = $imageProcessingData['sourceFile'];
                
                $targetFolder = 'store_data/posts/draft/' . $userFolder . '/' . $foldername;
                $targetFile = $targetFolder . '/' . $filename;

                Storage::disk('public')->makeDirectory($targetFolder);
                
                if (Storage::disk('public')->move($sourceFile, $targetFile)) {
                    FilePostStored::create([
                        'post_id' => $job->id,
                        'file_store_an_id' => $job->id,
                        'filename' => $filename,
                        'foldername' => $userFolder . '/' . $foldername,
                        'status' => 'active',
                        'type' => 'job_post',
                        'order' => 'first',
                    ]);

                    $tempImage->delete();
                    Storage::disk('public')->deleteDirectory('temp_data_img/' . $foldername);
                } else {
                    Log::error('Failed to move file after job creation');
                }
            } catch (\Throwable $e) {
                Log::error('Image processing failed after job creation: ' . $e->getMessage());
            }
        }

        // Check if company info exists and queue generation if needed (silently)
        $this->checkAndGenerateCompanyInfo($request->input('companyName'));

        // Send confirmation email to user
        try {
            \Mail::to(Auth::user()->email)->send(new \App\Mail\FreePostSubmittedMail($job, 5 - ($freePostsCount + 1)));
        } catch (\Exception $e) {
            \Log::error('Failed to send free post confirmation email: ' . $e->getMessage());
            // Don't fail the request if email fails
        }

        return response()->json([
            'success' => true,
            'job' => $job,
            'job_post_id' => $job->id,
            'remaining' => 5 - ($freePostsCount + 1),
            'message' => 'Free tier job post created and published successfully!'
        ]);
    }
}