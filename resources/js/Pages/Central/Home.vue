<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    store_name: '',
    subdomain: ''
});

const submit = () => {
    form.post(route('tenant.register'), {
        onSuccess: (page) => {
            // Tangkap URL redirect dari response props (kalau controller return Inertia location)
            // Atau kalau lu return JSON, lu harus parse manual.
            // Asumsi: Controller lu return JSON { redirect_url: '...' }
            // Tapi karena Inertia form, lebih aman kalau controller redirect pake Inertia::location($url)
            
            // Contoh manual redirect kalau dapet response JSON (perlu tweak di controller):
            // window.location.href = page.props.redirect_url; 
            const targetUrl = page.props.flash.target_url;

            console.log('Dapet URL redirect:', targetUrl); // Cek console browser

            if (targetUrl) {
                // PAKSA BROWSER PINDAH (Hard Refresh)
                window.location.href = targetUrl;
            } else {
                alert('Toko jadi, tapi URL redirect gak kebaca cuk!');
            }
        },
        onError: () => {
            // Animasi goyang atau alert error
            // alert('Waduh, gagal cuk! Cek merah-merah.');
        }
    });
};
</script>

<template>
    <div class="main-container d-flex justify-content-center align-items-center min-vh-100">
        
        <div class="card shadow-lg border-0 rounded-4 p-4 card-onboarding">
            <div class="card-body">
                
                <div class="text-center mb-5">
                    <div class="icon-wrapper mb-3">
                        🚀
                    </div>
                    <h2 class="fw-bold text-dark">Mulai Bisnismu</h2>
                    <p class="text-muted">Bikin toko online sendiri cuma 5 detik cuk!</p>
                </div>

                <form @submit.prevent="submit">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small">NAMA TOKO</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                🏪
                            </span>
                            <input 
                                v-model="form.store_name" 
                                type="text" 
                                class="form-control bg-light border-start-0 ps-0" 
                                placeholder="Contoh: Toko Sepatu Gaul"
                                :class="{ 'is-invalid': form.errors.store_name }"
                            >
                        </div>
                        <div v-if="form.errors.store_name" class="invalid-feedback d-block mt-1">
                            {{ form.errors.store_name }}
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-semibold text-secondary small">ALAMAT TOKO (URL)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                🌐
                            </span>
                            <input 
                                v-model="form.subdomain" 
                                type="text" 
                                class="form-control bg-light border-start-0 border-end-0 ps-0 text-lowercase" 
                                placeholder="namatoko"
                                :class="{ 'is-invalid': form.errors.subdomain }"
                            >
                            <span class="input-group-text bg-primary text-white border-primary fw-bold">
                                .localhost
                            </span>
                        </div>
                        <div class="form-text text-muted small mt-1 ms-1">
                            Nanti toko lu bisa diakses di: <span class="fw-bold text-primary">{{ form.subdomain || 'namatoko' }}.localhost:8000</span>
                        </div>
                        <div v-if="form.errors.subdomain" class="invalid-feedback d-block mt-1">
                            {{ form.errors.subdomain }}
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="btn btn-primary w-100 btn-lg rounded-3 fw-bold py-3 shadow-sm btn-animate"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                        {{ form.processing ? 'Lagi Dibikin...' : 'Gas Bikin Toko Sekarang! 🔥' }}
                    </button>

                </form>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted">Udah punya toko? <a href="#" class="text-decoration-none fw-bold">Login Admin Sini</a></small>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom CSS biar gak pasaran */

/* Background Gradient Keren */
.main-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-family: 'Inter', sans-serif; /* Pastiin lu punya font bagus, atau pake default */
}

/* Card Styling */
.card-onboarding {
    width: 100%;
    max-width: 500px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px); /* Efek kaca dikit */
    animation: slideUp 0.5s ease-out;
}

/* Icon Roket */
.icon-wrapper {
    font-size: 3rem;
    animation: float 3s ease-in-out infinite;
}

/* Custom Input Group biar nyatu */
.input-group-text {
    border-color: #e2e8f0;
}
.form-control {
    border-color: #e2e8f0;
    font-weight: 500;
}
.form-control:focus {
    box-shadow: none;
    border-color: #764ba2; /* Warna ungu pas focus */
    background-color: #fff;
}
.form-control:focus + .input-group-text, 
.input-group-text:has(+ .form-control:focus) {
    border-color: #764ba2;
}

/* Tombol */
.btn-primary {
    background: #4a00e0; /* Ungu gelap */
    background: -webkit-linear-gradient(to right, #8e2de2, #4a00e0);
    background: linear-gradient(to right, #8e2de2, #4a00e0);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Animations */
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}
</style>