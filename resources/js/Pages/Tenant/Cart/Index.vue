<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

/**
 * @description Shopping Cart management page.
 * Calculates order totals and manages line item resources for the authenticated user.
 */

const props = defineProps({ cartItems: Array });
const form = useForm({});

/**
 * Handle line item removal with SweetAlert2 confirmation.
 * Syncs with the backend to update the user's isolated cart session.
 */
const deleteItem = (id) => {
    Swal.fire({
        title: 'Remove Item?',
        text: "Are you sure you want to remove this product from your cart?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, Remove'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('cart.destroy', id), {
                onSuccess: () => Swal.fire('Deleted!', 'Item removed from cart.', 'success')
            });
        }
    });
};

/**
 * Utility for Indonesian Rupiah currency formatting.
 */
const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { 
    style: 'currency', 
    currency: 'IDR', 
    minimumFractionDigits: 0 
}).format(n);

/**
 * Aggregate the total cost of all items in the current collection.
 */
const total = (items) => items.reduce((acc, item) => acc + (item.product.price * item.quantity), 0);
</script>

<template>
    <div class="glass-layout min-vh-100 p-4">
        <div class="container" style="max-width: 800px;">
            <div class="d-flex justify-content-between align-items-end mb-4 px-2">
                <div>
                    <h2 class="fw-bold mb-0">Shopping Cart</h2>
                    <p class="text-muted small mb-0">Review your selected items before checkout.</p>
                </div>
                <Link href="/" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold text-uppercase">
                    Continue Shopping
                </Link>
            </div>

            <div class="glass-card p-4 shadow-sm border-0">
                <div v-for="item in cartItems" :key="item.id" class="d-flex justify-content-between align-items-center border-bottom py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="item-placeholder">🛍️</div>
                        <div>
                            <h5 class="fw-bold m-0 text-dark">{{ item.product.name }}</h5>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">
                                {{ item.quantity }} Unit(s) &times; {{ formatRupiah(item.product.price) }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-4">
                        <span class="fw-bold text-primary fs-5">{{ formatRupiah(item.product.price * item.quantity) }}</span>
                        <button @click="deleteItem(item.id)" class="btn btn-link text-danger p-0" title="Remove Item">
                            <i class="bi bi-trash"></i> <span class="small fw-bold">Remove</span>
                        </button>
                    </div>
                </div>

                <div v-if="cartItems.length === 0" class="text-center py-5">
                    <div class="fs-1 mb-3">🛒</div>
                    <h5 class="text-muted fw-bold">Your cart is empty</h5>
                    <Link href="/" class="btn btn-primary rounded-pill px-4 mt-3">Start Shopping</Link>
                </div>

                <div v-else class="mt-4 pt-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-0 text-uppercase fw-bold">Total Estimated Amount</p>
                        <h3 class="fw-bold text-dark m-0">{{ formatRupiah(total(cartItems)) }}</h3>
                    </div>
                    <button class="btn btn-success btn-glow px-5 py-3 rounded-pill fw-bold text-uppercase">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* High-End Styling Refinement */
.glass-layout { 
    background-color: #f8fafc; 
    background-image: radial-gradient(#e2e8f0 1px, transparent 1px); 
    background-size: 30px 30px; 
}

.glass-card { 
    background: rgba(255, 255, 255, 0.85); 
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 24px; 
}

.item-placeholder {
    width: 60px; height: 60px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
}

.btn-glow { 
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
    color: white; 
    border: none;
    box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
    transition: all 0.3s ease;
}

.btn-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
}
</style>