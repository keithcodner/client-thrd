<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Http\Controllers\Controller;
use App\Models\Shopping\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManageProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::with('user');

        // Handle search
        if ($request->has('productName') && $request->productName) {
            $query->where('productName', 'like', '%' . $request->productName . '%');
        }

        if ($request->has('productCode') && $request->productCode) {
            $query->where('productCode', 'like', '%' . $request->productCode . '%');
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        $searchParams = $request->only(['productName', 'productCode', 'status', 'type']);

        return Inertia::render('Admin/ProductManagement/Products', [
            'products' => $products,
            'searchParams' => $searchParams
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'productCode' => 'required|string|max:15|unique:products',
            'productName' => 'required|string|max:70',
            'productLine' => 'required|string|max:50',
            'productScale' => 'required|string|max:10',
            'productVendor' => 'required|string|max:50',
            'productDescription' => 'required|string',
            'quantityInStock' => 'required|integer|min:0',
            'buyPrice' => 'required|numeric|min:0',
            'MSRP' => 'required|numeric|min:0',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'user_id' => 'nullable|exists:users,id'
        ]);

        Products::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully'
        ]);
    }

    public function show(Products $product)
    {
        $product->load('user');

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function update(Request $request, Products $product)
    {
        $request->validate([
            'productCode' => 'required|string|max:15|unique:products,productCode,' . $product->id,
            'productName' => 'required|string|max:70',
            'productLine' => 'required|string|max:50',
            'productScale' => 'required|string|max:10',
            'productVendor' => 'required|string|max:50',
            'productDescription' => 'required|string',
            'quantityInStock' => 'required|integer|min:0',
            'buyPrice' => 'required|numeric|min:0',
            'MSRP' => 'required|numeric|min:0',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    }

    public function destroy(Products $product)
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ]);
        }
    }

    public function updateProductStatus(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|string'
        ]);

        $product = Products::findOrFail($request->product_id);

        switch ($request->action) {
            case 'activate':
                $product->status = 'active';
                $message = 'Product activated successfully';
                break;
            case 'deactivate':
                $product->status = 'inactive';
                $message = 'Product deactivated successfully';
                break;
            case 'featured':
                $product->mostPopular = 'yes';
                $message = 'Product marked as featured successfully';
                break;
            case 'unfeatured':
                $product->mostPopular = 'no';
                $message = 'Product removed from featured successfully';
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function getUsersForDropdown()
    {
        $users = User::select('id', 'firstname', 'lastname', 'email')
                    ->orderBy('firstname')
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')'
                        ];
                    });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}