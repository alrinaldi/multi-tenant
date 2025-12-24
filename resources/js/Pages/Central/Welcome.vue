<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="glass-layout min-vh-100 d-flex flex-column justify-content-center align-items-center position-relative overflow-hidden p-3">
        
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="glass-card text-center p-5" style="max-width: 650px; width: 100%;">
            
            <div class="icon-box mb-4 mx-auto">🚀</div>
            
            <h1 class="fw-bold display-4 mb-3 text-dark">SaaS Platform</h1>
            <p class="lead mb-5 text-secondary" style="font-size: 1.1rem;">
                Kelola ribuan toko online dalam satu dashboard canggih. <br>
                Cepat, aman, dan futuristik.
            </p>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <Link href="/login" class="btn btn-primary btn-glow px-5 py-3 fw-bold rounded-pill">
                    Login Owner
                </Link>
                
                <Link href="/register" class="btn btn-light btn-glass px-5 py-3 fw-bold rounded-pill text-primary">
                    Daftar Baru
                </Link>
            </div>

            <div class="mt-5 pt-4 border-top border-secondary border-opacity-10">
                <small class="text-muted opacity-75">&copy; 2024 Tenant System Pro. All rights reserved.</small>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* BACKGROUND GLOBAL */
.glass-layout {
    background-color: #f3f5f9;
    background-image: radial-gradient(#e1e6ed 1px, transparent 1px);
    background-size: 30px 30px;
    z-index: 1;
}

/* ANIMATED BLOBS */
.blob {
    position: absolute; border-radius: 50%; filter: blur(70px); z-index: -1;
    animation: float 10s infinite alternate;
}
.blob-1 { top: 10%; left: 10%; width: 300px; height: 300px; background: #667eea; opacity: 0.6; }
.blob-2 { bottom: 10%; right: 10%; width: 350px; height: 350px; background: #764ba2; opacity: 0.6; }
.blob-3 { top: 40%; left: 40%; width: 200px; height: 200px; background: #8fd3f4; opacity: 0.5; animation-delay: -5s; }

@keyframes float {
    0% { transform: translate(0, 0) rotate(0deg); }
    100% { transform: translate(30px, 50px) rotate(10deg); }
}

/* GLASS CARD STYLE */
.glass-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 35px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.05);
}

/* ICON BOX */
.icon-box {
    width: 90px; height: 90px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    display: flex; align-items: center; justify-content: center;
    font-size: 45px; color: white;
    box-shadow: 0 15px 30px rgba(118, 75, 162, 0.3);
}

/* BUTTONS */
.btn-glow {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none; transition: 0.3s;
    color: white;
}
.btn-glow:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 15px 30px rgba(118, 75, 162, 0.4); 
    color: white;
}

.btn-glass {
    background: rgba(255,255,255,0.6); 
    border: 1px solid white;
    transition: 0.3s;
}
.btn-glass:hover { 
    background: white; 
    transform: translateY(-3px); 
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}
</style>