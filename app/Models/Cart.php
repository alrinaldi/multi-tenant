<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cart
 * Represents the shopping cart entity within the tenant-specific database.
 * This model facilitates the link between users and their selected products.
 */
class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    /**
     * Get the product associated with the cart item.
     * Establishes a Many-to-One relationship to the Product model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the user who owns the cart item.
     * Links the cart entry to a specific user within the tenant's isolated user table.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}