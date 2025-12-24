<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

/**
 * Class HomeController
 * Serves the public-facing storefront for individual tenants.
 */
class HomeController extends Controller
{
    /**
     * Display the tenant's homepage with a cached product list.
     * * This method implements Cache Tagging to ensure high performance 
     * while maintaining strict data isolation between tenants.
     * * @return \Inertia\Response
     */
    public function index()
    {
        // 1. Generate a unique cache tag based on the current Tenant ID to prevent data leakage.
        $tenantId = tenant('id');
        $tag = "tenant_{$tenantId}";

        /** * 2. Cache-Aside Pattern Implementation:
         * Retrieve products from cache if available, otherwise fetch from DB and store for 60 minutes.
         * The 'tags' method allows for targeted cache invalidation when products are updated.
         */
        $products = Cache::tags([$tag])->remember('products_home_list', 3600, function () {
            return Product::latest()->get();
        });

        // 3. Render the Inertia view with reactive props.
        return Inertia::render('Tenant/Home', [
            // Dynamically retrieved store name from the tenant's central configuration.
            'storeName' => tenant('store_name'),
            'products' => $products,
            
            /**
             * User authentication state. 
             * Although globally shared via HandleInertiaRequests, explicit declaration 
             * can be used for local component clarity.
             */
            'auth' => ['user' => auth()->user()] 
        ]);
    }
}