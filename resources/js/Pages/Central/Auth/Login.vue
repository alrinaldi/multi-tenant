<script setup>
import { useForm, Link } from '@inertiajs/vue3';

/**
 * @description Authentication page for the Central Administration Portal.
 * Handles secure session establishment for Platform Owners.
 */

defineProps({ status: String });

// Initialize authentication form state
const form = useForm({
    email: '',
    password: '',
});

/**
 * Submit the credentials to the central authentication endpoint.
 * Regenerates session on successful login to prevent fixation attacks.
 */
const submit = () => {
    form.post(route('login'), { 
        onFinish: () => form.reset('password') 
    });
};
</script>

<template>
    <div class="premium-bg min-vh-100 d-flex align-items-center justify-content-center position-relative overflow-hidden">
        
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>

        <div class="glass-card p-5 text-center">
            
            <div class="mb-4">
                <div class="icon-glass mb-3">🛡️</div>
                <h3 class="fw-bold text-dark">Central Access</h3>
                <p class="text-secondary small">Authenticate to access the management portal.</p>
            </div>

            <div v-if="status" class="alert alert-success py-2 small mb-3 text-start">{{ status }}</div>

            <form @submit.prevent="submit">
                <div class="mb-3 text-start">
                    <label class="form-label ms-1 small fw-bold text-muted text-uppercase">Email Address</label>
                    <input v-model="form.email" type="email" class="form-control glass-input" placeholder="admin@platform.com" required>
                    <div v-if="form.errors.email" class="text-danger small mt-1 ms-1">{{ form.errors.email }}</div>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label ms-1 small fw-bold text-muted text-uppercase">Security Password</label>
                    <input v-model="form.password" type="password" class="form-control glass-input" placeholder="••••••••" required>
                    <div v-if="form.errors.password" class="text-danger small mt-1 ms-1">{{ form.errors.password }}</div>
                </div>

                <button type="submit" :disabled="form.processing" class="btn btn-primary w-100 btn-glow py-3 fw-bold rounded-pill text-uppercase">
                    {{ form.processing ? 'Verifying...' : 'Authenticate Now' }}
                </button>
            </form>

            <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
                <span class="text-muted small">New to the platform? </span>
                <Link :href="route('register')" class="fw-bold text-primary text-decoration-none">Create an Account</Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* 1. Premium Background with Subtle Gradients */
.premium-bg {
    background-color: #e0e5ec;
    background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    z-index: 1;
}

/* 2. Animated Decorative Shapes */
.shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: -1;
    animation: float 10s infinite alternate;
}
.shape-1 {
    width: 300px; height: 300px;
    background: #4facfe; 
    top: 10%; left: 20%;
}
.shape-2 {
    width: 250px; height: 250px;
    background: #f093fb; 
    bottom: 15%; right: 20%;
    animation-delay: -5s;
}

@keyframes float {
    0% { transform: translate(0, 0) rotate(0deg); }
    100% { transform: translate(30px, 50px) rotate(20deg); }
}

/* 3. Glassmorphism Card Effect */
.glass-card {
    width: 100%; max-width: 420px;
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 28px;
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
}

/* 4. Aesthetic Input Fields */
.glass-input {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 14px;
    padding: 14px 18px;
    transition: all 0.3s ease;
}
.glass-input:focus {
    background: white;
    box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.15);
    border-color: #4facfe;
    outline: none;
}

/* 5. Centralized Icon Wrapper */
.icon-glass {
    font-size: 32px;
    background: rgba(255, 255, 255, 0.5);
    width: 70px; height: 70px;
    margin: 0 auto;
    display: flex; align-items: center; justify-content: center;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

/* 6. High-Conversion Glow Button */
.btn-glow {
    background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
    border: none;
    box-shadow: 0 4px 20px rgba(79, 172, 254, 0.3);
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}
.btn-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.5);
    color: white;
}
</style>