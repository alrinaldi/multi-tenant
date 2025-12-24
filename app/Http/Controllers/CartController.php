<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Class CartController
 * Manages the shopping cart lifecycle for authenticated users within a tenant store.
 */
class CartController extends Controller {

    /**
     * Display the user's shopping cart.
     * Fetches items associated with the authenticated user and eager loads product details.
     * * @return \Inertia\Response
     */
    public function index() {
        return Inertia::render('Tenant/Cart/Index', [
            // Scopes the query to the authenticated user's ID to ensure data privacy.
            'cartItems' => Cart::with('product')->where('user_id', Auth::id())->get()
        ]);
    }

    /**
     * Add a product to the cart or increment quantity if it already exists.
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        // Enforce strict validation to maintain data integrity.
        $request->validate([
            'product_id' => 'required|exists:products,id', 
            'quantity' => 'required|integer|min:1'
        ]);
        
        // Check if the item already exists in the user's isolated cart session.
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->first();
        
        if ($cart) { 
            // Atomic increment to prevent race conditions during quantity updates.
            $cart->increment('quantity', $request->quantity); 
        } 
        else {
            // Create a new cart entry linked to the authenticated user.
            Cart::create([
                'user_id' => Auth::id(), 
                'product_id' => $request->product_id, 
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->back();
    }

    /**
     * Remove an item from the shopping cart.
     * * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id) {
        /**
         * Security Check: Ensure the cart item belongs to the authenticated user 
         * before deletion to prevent unauthorized resource access.
         */
        Cart::where('id', $id)->where('user_id', Auth::id())->delete();

        return redirect()->back();
    }
}