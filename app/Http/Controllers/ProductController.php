<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Class ProductController
 * Manages the product catalog for a specific tenant instance.
 */
class ProductController extends Controller
{
    /**
     * Fetch and display the list of products for the administrative dashboard.
     * * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Tenant/Products/Index', [
            // Retrieves products scoped to the current tenant connection.
            'products' => Product::latest()->get()
        ]);
    }

    /**
     * Validate and store a new product, including physical asset handling.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Enforce strict data types and image constraints for security and consistency.
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|max:2048' // Limits upload to 2MB to optimize storage.
        ]);

        /**
         * Asset Storage Logic:
         * If an image is present, store it within the 'public' disk.
         * In this Multi-tenant setup, the file is automatically routed to:
         * storage/tenant{id}/app/public/products
         */
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            
            // Persist the relative storage path to the database.
            $data['image'] = $path; 
        }

        // Atomic creation of the product record in the tenant's isolated database.
        Product::create($data);
        
        return redirect()->back();
    }
}