<script setup>
import { useForm, Link } from '@inertiajs/vue3';

/**
 * @description Tenant Owner registration page for the SaaS ecosystem.
 * This form establishes the primary administrative account in the Central Database.
 */

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

/**
 * Submit the registration request to the central server.
 * Password confirmation is strictly validated on the backend.
 */
const submit = () => {
    form.post(route('register'), { 
        onFinish: () => form.reset('password', 'password_confirmation') 
    });
};
</script>

<template>
    <div class="premium-bg min-vh-100 d-flex align-items-center justify-content-center position-relative overflow-hidden p-3">
        
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>

        <div class="glass-card p-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Create Account</h3>
                <p class="text-secondary small">Start provisioning your multi-tenant SaaS infrastructure today.</p>
            </div>

            <form @submit.prevent="submit">
                <div class="mb-3">
                    <label class="form-label ms-1 small fw-bold text-muted text-uppercase">Full Name</label>
                    <input v-model="form.name" type="text" class="form-control glass-input" placeholder="Enter your full name">
                    <div v-if="form.errors.name" class="text-danger small mt-1 ms-1">{{ form.errors.name }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label ms-1 small fw-bold text-muted text-uppercase">Email Address</label>
                    <input v-model="form.email" type="email" class="form-control glass-input" placeholder="email@example.com">
                    <div v-if="form.errors.email" class="text-danger small mt-1 ms-1">{{ form.errors.email }}</div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label ms-1 small fw-bold text-muted text-uppercase">Password</label>
                        <input v-model="form.password" type="password" class="form-control glass-input" placeholder="••••••••">
                    </div>
                    <div class="col-6 mb-4">
                        <label class="form-label ms-1 small fw-bold text-muted text-uppercase">Confirm</label>
                        <input v-model="form.password_confirmation" type="password" class="form-control glass-input" placeholder="••••••••">
                    </div>
                </div>
                <div v-if="form.errors.password" class="text-danger small mb-3 ms-1">{{ form.errors.password }}</div>

                <button type="submit" :disabled="form.processing" class="btn btn-primary w-100 btn-glow py-3 fw-bold rounded-pill text-uppercase">
                    {{ form.processing ? 'Provisioning...' : 'Register Account' }}
                </button>
            </form>

            <div class="mt-4 pt-3 border-top border-secondary border-opacity-10 text-center">
                <span class="text-muted small">Already have an account? </span>
                <Link :href="route('login')" class="fw-bold text-primary text-decoration-none">Sign In</Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Background Styling with Soft Gradients */
.premium-bg { background-image: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); z-index: 1; }

.shape { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; animation: float 10s infinite alternate; }
.shape-1 { width: 400px; height: 400px; background: #a18cd1; top: -10%; left: -10%; opacity: 0.6; }
.shape-2 { width: 300px; height: 300px; background: #fbc2eb; bottom: -10%; right: -10%; opacity: 0.6; }

@keyframes float { 0% { transform: translate(0, 0); } 100% { transform: translate(20px, 40px); } }

/* Professional Glassmorphism Container */
.glass-card {
    width: 100%; max-width: 500px;
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 28px;
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
}

/* Modern Input Controls */
.glass-input {
    background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.6);
    border-radius: 14px; padding: 13px 18px; transition: all 0.3s ease;
}
.glass-input:focus { 
    background: white; 
    box-shadow: 0 0 0 4px rgba(161, 140, 209, 0.15); 
    border-color: #a18cd1; 
    outline: none;
}

/* Call-to-Action Glowing Button */
.btn-glow {
    background: linear-gradient(to right, #a18cd1 0%, #fbc2eb 100%);
    border: none; 
    box-shadow: 0 4px 20px rgba(161, 140, 209, 0.4); 
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}
.btn-glow:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 8px 30px rgba(161, 140, 209, 0.6); 
    color: white;
}
</style>