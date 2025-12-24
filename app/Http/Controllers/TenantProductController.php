<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Class TenantProductController
 * * Manages administrative product operations including CRUD and asset management
 * within an isolated multi-tenant environment.
 */
class TenantProductController extends Controller {
    
    /**
     * Display a listing of the tenant's products.
     * * @return \Inertia\Response
     */
    public function index() { 
        return Inertia::render('Tenant/Product/Index', [
            'products' => Product::latest()->get()
        ]); 
    }

    /**
     * Store a newly created product in the isolated tenant database.
     * * Implements multi-part form data handling for image uploads, 
     * automatically routing assets to tenant-specific storage paths.
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'description']);

        if ($request->hasFile('image')) {
            /** * Files are stored within the tenant's private directory:
             * storage/tenant{id}/app/public/products
             */
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        
        $this->clearTenantCache();
        return redirect()->back()->with('message', 'Product added successfully.');
    }

    /**
     * Update the specified product resource.
     * * Manages physical storage cleanup by deleting obsolete assets before 
     * persisting new uploads to prevent storage bloating.
     * * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'description']);

        if ($request->hasFile('image')) {
            // Asset Purge: Remove the previous file from physical storage to optimize disk space.
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Persist new asset to the tenant-specific public disk.
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        
        $this->clearTenantCache();
        return redirect()->back();
    }

    /**
     * Remove the specified product and its associated assets.
     * * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        /** * Lifecycle Hook: Ensure physical assets are purged from the filesystem 
         * before the database record is removed.
         */
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        
        $this->clearTenantCache();
        return redirect()->back();
    }

    /**
     * Flush tenant-specific cache tags.
     * * This ensures that storefront changes are immediate while maintaining 
     * high performance via the Cache-Aside pattern.
     * * @return void
     */
    private function clearTenantCache()
    {
        Cache::tags(["tenant_" . tenant('id')])->flush();
    }
}