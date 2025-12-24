<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Swal from 'sweetalert2';

/**
 * @description Tenant Storefront Component.
 * Features a reactive product grid with session-aware shopping cart integration.
 */

const props = defineProps({ 
    storeName: String, 
    products: Array 
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const form = useForm({
    product_id: null,
    quantity: 1
});

/**
 * Handle Add to Cart logic with professional feedback.
 * Uses SweetAlert2 for session validation and success notification.
 */
const addToCart = (id) => {
    if (!user.value) {
        // Professional Modal for Authentication Required
        Swal.fire({
            title: 'Authentication Required',
            text: 'Please sign in to your account to manage your shopping cart.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sign In Now',
            cancelButtonText: 'Later'
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('login'));
            }
        });
    } else {
        form.product_id = id;
        form.post(route('cart.store'), {
            preserveScroll: true,
            onSuccess: () => {
                // Professional Toast Notification for Success
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Added to cart successfully!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    iconColor: '#4f46e5',
                });
            }
        });
    }
};

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0 
    }).format(number);
};
</script>

<template>
    <div class="glass-layout min-vh-100 p-4">
        
        <nav class="glass-nav navbar fixed-top px-4 d-flex justify-content-between">
            <span class="navbar-brand fw-bold text-dark">🏪 {{ storeName }}</span>
            <div class="d-flex gap-2 align-items-center">
                <template v-if="user">
                    <span class="d-none d-md-block me-2 fw-semibold text-muted small">
                        Welcome, {{ user.name }}
                    </span>
                    <Link href="/cart" class="btn btn-outline-primary rounded-pill px-3 fw-bold small">My Cart</Link>
                    <div v-if="user.role === 'admin'">
                        <Link :href="route('tenant.products.index')" class="btn btn-dark rounded-pill px-3 fw-bold small">Dashboard</Link>
                    </div>
                    <Link href="/logout" method="post" as="button" class="btn btn-outline-danger rounded-pill px-3 fw-bold small">Logout</Link>
                </template>
                <template v-else>
                    <Link href="/login" class="btn btn-primary btn-glow rounded-pill px-4 fw-bold">Sign In / Register</Link>
                </template>
            </div>
        </nav>

        <div style="height: 100px;"></div> 
        
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold display-5 text-dark">Welcome to {{ storeName }}</h1>
                <p class="text-muted lead">Discover our premium collection of curated products.</p>
            </div>

            <div v-if="products.length === 0" class="text-center py-5">
                <div class="display-1 opacity-25 mb-3">📦</div>
                <h5 class="text-muted fw-bold">No Products Available</h5>
                <p class="small text-secondary">Our catalog is currently being updated. Please check back soon.</p>
            </div>

            <div v-else class="row g-4">
                <div v-for="product in products" :key="product.id" class="col-sm-6 col-md-4 col-lg-3 mb-2">
                    <div class="glass-card h-100 d-flex flex-column hover-effect border-0">
                        
                        <div class="product-image-container">
                            <img 
                                v-if="product.image_url" 
                                :src="product.image_url" 
                                class="product-img"
                                :alt="product.name"
                            >
                            <div v-else class="no-image-placeholder">
                                <span class="opacity-25 fs-1">🖼️</span>
                            </div>
                        </div>

                        <div class="p-4 d-flex flex-column h-100">
                            <h6 class="fw-bold mb-1 text-dark text-truncate">{{ product.name }}</h6>
                            <p class="text-primary fw-bold fs-5 mb-3">{{ formatRupiah(product.price) }}</p>
                            
                            <div class="mt-auto">
                                <div class="mb-3">
                                    <span :class="product.stock > 0 ? 'badge bg-light text-muted border' : 'badge bg-danger-subtle text-danger'" class="rounded-pill px-3">
                                        {{ product.stock > 0 ? 'In Stock: ' + product.stock : 'Out of Stock' }}
                                    </span>
                                </div>
                                <button 
                                    @click="addToCart(product.id)" 
                                    class="btn btn-primary w-100 btn-glow fw-bold py-2 rounded-pill text-uppercase"
                                    style="font-size: 12px; letter-spacing: 0.5px;"
                                    :disabled="form.processing || product.stock <= 0"
                                >
                                    <span v-if="!user">Sign In to Purchase</span>
                                    <span v-else-if="product.stock <= 0">Sold Out</span>
                                    <span v-else>Add to Cart</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Image & Container Logic */
.product-image-container {
    height: 220px;
    width: 100%;
    overflow: hidden;
    background: #fdfdfd;
}
.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.hover-effect:hover .product-img {
    transform: scale(1.1);
}
.no-image-placeholder {
    height: 100%;
    background-color: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Glassmorphism Styling */
.glass-layout { 
    background-color: #f8fafc; 
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px); 
    background-size: 30px 30px; 
}
.glass-nav { 
    background: rgba(255, 255, 255, 0.85); 
    backdrop-filter: blur(15px); 
    border-bottom: 1px solid rgba(0,0,0,0.05); 
    box-shadow: 0 4px 20px rgba(0,0,0,0.02); 
}
.glass-card { 
    background: #ffffff; 
    border-radius: 24px; 
    transition: all 0.3s ease; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.03);
}
.hover-effect:hover { 
    transform: translateY(-8px); 
    box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; 
}

/* Button & UI Accents */
.btn-glow { 
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
    color: white; 
    border: none; 
}
.btn-glow:hover:not(:disabled) { 
    filter: brightness(1.1);
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
}
.badge { font-weight: 600; font-size: 11px; }
</style>