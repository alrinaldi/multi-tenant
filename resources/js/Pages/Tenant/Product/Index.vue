<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

/**
 * @description Product Management Component for Tenant Administration.
 * Handles CRUD operations for the product catalog with multi-tenant data isolation.
 */

const props = defineProps({ products: Array });

/**
 * Form state for resource creation.
 */
const form = useForm({ 
    name: '', price: '', stock: '', description: '', image: null 
});

/**
 * Form state for resource updates.
 * Implements Method Spoofing (_method: PUT) for multipart/form-data support.
 */
const editForm = useForm({
    id: null, name: '', price: '', stock: '', description: '', image: null,
    _method: 'PUT' 
});

const imagePreview = ref(null);
const editImagePreview = ref(null);

/**
 * Process file input and generate local BLOB URL for real-time preview.
 */
const handleFile = (e) => {
    const file = e.target.files[0];
    form.image = file;
    if (file) imagePreview.value = URL.createObjectURL(file);
};

const handleEditFile = (e) => {
    const file = e.target.files[0];
    editForm.image = file;
    if (file) editImagePreview.value = URL.createObjectURL(file);
};

/**
 * Persist a newly created product to the tenant database.
 */
const submit = () => { 
    form.post(route('tenant.products.store'), { 
        forceFormData: true, 
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'New product has been successfully registered.',
                confirmButtonColor: '#4f46e5'
            });
        } 
    }); 
};

/**
 * Initialize the update modal with existing product data.
 */
const openEditModal = (product) => {
    editForm.id = product.id;
    editForm.name = product.name;
    editForm.price = product.price;
    editForm.stock = product.stock;
    editImagePreview.value = product.image_url;
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
};

/**
 * Submit product modifications to the server.
 */
const updateProduct = () => {
    editForm.post(route('tenant.products.update', editForm.id), {
        forceFormData: true,
        onSuccess: () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            modal.hide();
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Product information has been successfully modified.',
                confirmButtonColor: '#4f46e5'
            });
        }
    });
};

/**
 * Handle resource deletion with confirmation dialog.
 */
const deleteProduct = (id) => {
    Swal.fire({
        title: 'Confirm Deletion?',
        text: "This action is permanent and will remove the product from the inventory.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('tenant.products.destroy', id), {
                onSuccess: () => Swal.fire('Deleted!', 'The product has been removed.', 'success')
            });
        }
    });
};

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
</script>

<template>
    <div class="glass-layout min-vh-100 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Inventory Management</h2>
                    <p class="text-muted">Monitor and manage your store's product catalog.</p>
                </div>
                <Link href="/" class="btn btn-light shadow-sm rounded-pill px-4">← View Storefront</Link>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="glass-card p-4 sticky-top" style="top: 2rem;">
                        <h5 class="fw-bold mb-3">Add New Resource</h5>
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase">Product Asset</label>
                                <div class="image-upload-wrapper mb-2">
                                    <img v-if="imagePreview" :src="imagePreview" class="preview-img rounded-3">
                                    <div v-else class="placeholder-upload rounded-3"><span>Select Image</span></div>
                                    <input type="file" @input="handleFile" class="file-input" accept="image/*">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase">Product Name</label>
                                <input v-model="form.name" class="form-control glass-input" placeholder="e.g. Premium T-Shirt">
                            </div>
                            <div class="row">
                                <div class="col-7 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Price</label>
                                    <input v-model="form.price" type="number" class="form-control glass-input">
                                </div>
                                <div class="col-5 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Stock</label>
                                    <input v-model="form.stock" type="number" class="form-control glass-input text-center">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-glow py-2 fw-bold" :disabled="form.processing">
                                Register Product
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="glass-card p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr class="small text-uppercase text-muted">
                                        <th class="text-start">Product Details</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Availability</th>
                                        <th class="text-end">Management</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="p in products" :key="p.id">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img :src="p.image_url" class="rounded-3 me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                                <span class="fw-semibold text-dark">{{ p.name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold text-primary">{{ formatRupiah(p.price) }}</td>
                                        <td class="text-center">
                                            <span :class="p.stock < 10 ? 'badge bg-danger-subtle text-danger' : 'badge bg-success-subtle text-success'">
                                                {{ p.stock }} Units
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button @click="openEditModal(p)" class="btn btn-sm btn-outline-primary rounded-pill me-2 px-3">Edit</button>
                                            <button @click="deleteProduct(p.id)" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-bold">Update Inventory Resource</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="updateProduct">
                        <div class="modal-body">
                            <div class="mb-3 text-center">
                                <div class="image-upload-wrapper mx-auto" style="width: 150px; height: 150px;">
                                    <img v-if="editImagePreview" :src="editImagePreview" class="preview-img rounded-3">
                                    <input type="file" @input="handleEditFile" class="file-input" accept="image/*">
                                </div>
                                <small class="text-muted mt-2 d-block">Click to update product image</small>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-uppercase">Product Name</label>
                                <input v-model="editForm.name" class="form-control glass-input">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="small fw-bold text-uppercase">Price</label>
                                    <input v-model="editForm.price" type="number" class="form-control glass-input">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="small fw-bold text-uppercase">Stock Level</label>
                                    <input v-model="editForm.stock" type="number" class="form-control glass-input">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 pb-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-glow fw-bold" :disabled="editForm.processing">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.glass-layout { background-color: #f8fafc; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 24px 24px; }
.glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 15px 35px rgba(0,0,0,0.03); }
.glass-input { background: rgba(255, 255, 255, 0.6); border: 1.5px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; }
.glass-input:focus { background: #fff; border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
.btn-glow { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); }
.btn-glow:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4); color: white; }
.image-upload-wrapper { position: relative; height: 120px; cursor: pointer; border: 2px dashed #e2e8f0; border-radius: 12px; overflow: hidden; transition: border-color 0.2s; }
.image-upload-wrapper:hover { border-color: #4f46e5; }
.file-input { position: absolute; top: 0; left: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 5; }
.preview-img { width: 100%; height: 100%; object-fit: cover; }
.placeholder-upload { height: 100%; display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #94a3b8; font-weight: 500; font-size: 0.85rem; }
</style>