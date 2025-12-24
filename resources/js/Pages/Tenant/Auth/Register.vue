<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

/**
 * @description Customer registration page for the tenant's storefront.
 * Handles account creation within the isolated tenant database.
 */

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

/**
 * Submit the registration data using a relative path.
 * This ensures the request is handled by the current tenant's subdomain.
 */
const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Account" />

    <div class="glass-layout min-vh-100 d-flex align-items-center justify-content-center p-4">
        <div class="glass-card p-5" style="width: 100%; max-width: 500px;">
            
            <div class="text-center mb-4">
                <div class="icon-box mx-auto mb-3">📝</div>
                <h2 class="fw-bold text-dark">Join Us</h2>
                <p class="text-secondary">Create an account for a faster checkout experience.</p>
            </div>

            <form @submit.prevent="submit">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary text-uppercase">Full Name</label>
                    <input v-model="form.name" type="text" class="form-control glass-input" placeholder="Enter your full name" required autofocus>
                    <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary text-uppercase">Email Address</label>
                    <input v-model="form.email" type="email" class="form-control glass-input" placeholder="name@example.com" required>
                    <div v-if="form.errors.email" class="text-danger small mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-secondary text-uppercase">Password</label>
                        <input v-model="form.password" type="password" class="form-control glass-input" placeholder="••••••" required>
                        <div v-if="form.errors.password" class="text-danger small mt-1">{{ form.errors.password }}</div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-secondary text-uppercase">Confirm</label>
                        <input v-model="form.password_confirmation" type="password" class="form-control glass-input" placeholder="••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-glow fw-bold py-3 rounded-pill text-uppercase" :disabled="form.processing">
                    {{ form.processing ? 'Creating Account...' : 'Register Now' }}
                </button>
            </form>

            <div class="text-center mt-4 border-top pt-3">
                <p class="text-muted small mb-2">Already have an account?</p>
                <Link href="/login" class="text-primary fw-bold text-decoration-none">Sign In here</Link>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Consistently styled with Login.vue for a seamless brand experience */
.glass-layout { 
    background-color: #f3f5f9; 
    background-image: radial-gradient(#e1e6ed 1px, transparent 1px); 
    background-size: 30px 30px; 
}

.glass-card { 
    background: rgba(255, 255, 255, 0.75); 
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(255, 255, 255, 0.8); 
    border-radius: 28px; 
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); 
}

.glass-input { 
    background: rgba(255, 255, 255, 0.6); 
    border: 1px solid #e5e7eb; 
    padding: 13px 18px; 
    border-radius: 14px; 
    transition: all 0.3s ease; 
}

.glass-input:focus { 
    background: #fff; 
    border-color: #667eea; 
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15); 
    outline: none; 
}

.icon-box { 
    width: 60px; height: 60px; 
    background: linear-gradient(135deg, #f8f9ff 0%, #eef2ff 100%); 
    color: #4f46e5; 
    border-radius: 20px; 
    display: flex; align-items: center; justify-content: center; 
    font-size: 24px; 
}

.btn-glow { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
    border: none; 
    box-shadow: 0 4px 20px rgba(118, 75, 162, 0.3); 
    transition: all 0.2s ease; 
    letter-spacing: 0.5px;
}

.btn-glow:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 8px 25px rgba(118, 75, 162, 0.4); 
    color: white;
}
</style>