<script setup>
import { useForm, Link } from '@inertiajs/vue3';

/**
 * @description Central Administration Dashboard for Platform Owners.
 * Manages tenant provisioning, domain routing, and infrastructure overview.
 */

const props = defineProps({ 
    user: Object,
    tenants: Array 
});

const form = useForm({
    store_name: '',
    subdomain: ''
});

/**
 * Handle new tenant registration and automated infrastructure setup.
 * Redirects to the new tenant's administration URL upon successful provisioning.
 */
const submit = () => {
    form.post(route('tenant.register'), {
        onSuccess: (page) => {
            const url = page.props.flash.target_url;
            if (url) window.location.href = url;
        }
    });
};

/**
 * Resolve the tenant's public storefront URL.
 */
const getStoreUrl = (subdomain) => {
    return `http://${subdomain}.localhost:8000`;
};
</script>

<template>
    <div class="dashboard-bg min-vh-100 pb-5">
        
        <nav class="navbar navbar-expand-lg glass-nav fixed-top px-4">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                    <div class="logo-circle">⚙️</div>
                    <span class="text-dark">SaaS Infrastructure</span>
                </a>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block">
                        <div class="fw-bold small text-dark">{{ user.name }}</div>
                        <div class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 10px;">SYSTEM ADMINISTRATOR</div>
                    </div>
                    <Link href="/logout" method="post" as="button" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                        Sign Out
                    </Link>
                </div>
            </div>
        </nav>

        <div style="height: 100px;"></div>

        <div class="container">
            <div class="row g-4">
                
                <div class="col-lg-5 mb-4">
                    <div class="position-relative h-100">
                        <div class="blob blob-1"></div>
                        
                        <div class="glass-card p-4 h-100">
                            <div class="mb-4">
                                <h4 class="fw-bold mb-1 text-dark">Instance Provisioning</h4>
                                <p class="text-muted small">Deploy a new tenant instance to the infrastructure.</p>
                            </div>
                            
                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-secondary ms-1 text-uppercase">Organization Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text glass-addon border-0">🏢</span>
                                        <input v-model="form.store_name" type="text" class="form-control glass-input" placeholder="e.g. Global Logistics">
                                    </div>
                                    <div v-if="form.errors.store_name" class="text-danger small mt-1 ms-1">{{ form.errors.store_name }}</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-secondary ms-1 text-uppercase">Subdomain Routing</label>
                                    <div class="input-group">
                                        <span class="input-group-text glass-addon border-0">🔗</span>
                                        <input v-model="form.subdomain" type="text" class="form-control glass-input" placeholder="subdomain">
                                        <span class="input-group-text glass-addon border-0 text-dark fw-bold">.localhost</span>
                                    </div>
                                    <div v-if="form.errors.subdomain" class="text-danger small mt-1 ms-1">{{ form.errors.subdomain }}</div>
                                </div>

                                <button type="submit" :disabled="form.processing" class="btn btn-primary w-100 btn-glow py-3 fw-bold rounded-4 text-uppercase">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ form.processing ? 'Configuring System...' : 'Deploy Instance' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="position-relative">
                        <div class="blob blob-2"></div>

                        <div class="glass-card p-4">
                            <h4 class="fw-bold mb-4 border-bottom pb-3">Active Instances ({{ tenants.length }})</h4>

                            <div v-if="tenants.length === 0" class="text-center py-5">
                                <div class="fs-1 mb-3">📡</div>
                                <h5 class="text-muted fw-bold">No Active Tenants Detected</h5>
                                <p class="small text-secondary px-5">The infrastructure is currently idle. Use the provisioning form to deploy your first tenant.</p>
                            </div>

                            <div v-else class="row g-3">
                                <div v-for="tenant in tenants" :key="tenant.id" class="col-md-6">
                                    <div class="tenant-card p-3 d-flex flex-column h-100">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="tenant-icon">📦</div>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold m-0 text-truncate text-dark">{{ tenant.store_name }}</h6>
                                                <small class="text-muted d-block text-truncate">ID: {{ tenant.id }}</small>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-auto d-flex gap-2">
                                            <a :href="getStoreUrl(tenant.id)" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill fw-bold">
                                                Public Link
                                            </a>
                                            <a :href="getStoreUrl(tenant.id) + '/login'" target="_blank" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold">
                                                Manage Settings
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* Refined Styling for Enterprise Look */
.dashboard-bg {
    background-color: #f6f9fc;
    background-image: radial-gradient(#e3e8f0 1px, transparent 1px);
    background-size: 30px 30px;
}

.glass-nav {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.logo-circle {
    width: 35px; height: 35px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 16px;
}

.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 28px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.03);
}

.glass-input {
    background: rgba(255, 255, 255, 0.6);
    border-radius: 0 12px 12px 0 !important;
}

.tenant-card {
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 18px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tenant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.06);
    border-color: #4facfe;
}

.btn-glow {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    letter-spacing: 0.5px;
}
</style>