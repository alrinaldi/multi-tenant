<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class Product
 * Represents a product entity within a specific tenant's catalog.
 * It handles metadata for items and their associated physical assets.
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'price', 'stock', 'image'];

    /**
     * The accessors to append to the model's array form.
     * This allows 'image_url' to be automatically available in Inertia/Vue props.
     */
    protected $appends = ['image_url'];

    /**
     * Accessor for the 'image_url' attribute.
     * Generates a fully qualified URL to the product image using the Tenancy Asset Helper.
     * * @description This ensures the URL points to the tenant-specific storage:
     * storage/tenant{id}/app/public/products
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        /** * The tenant_asset helper resolves the path based on the current tenant context.
         * Legacy accessor naming convention (get...Attribute) is used for maximum stability.
         */
        return tenant_asset($this->image);
    }
    
    /**
     * Define a One-to-Many relationship with the Cart model.
     * Allows tracking of how many times this product has been added to various user carts.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts() {
        return $this->hasMany(Cart::class);
    }
}