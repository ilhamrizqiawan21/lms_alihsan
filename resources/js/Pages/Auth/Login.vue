<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    branding: { type: Object, required: true },
    loginUrl: { type: String, required: true },
    year: { type: [String, Number], required: true },
});

const page = usePage();
const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const flash = computed(() => page.props.flash ?? {});
const title = computed(() => `Login - ${props.branding.school_short_name} ${props.branding.school_name}`);

function submit() {
    form.post(props.loginUrl, {
        preserveScroll: true,
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head :title="title" />

    <main class="login-page">
        <div class="login-wrapper">
            <section class="login-header" aria-labelledby="login-title">
                <div class="logo-circle">
                    <img
                        :src="branding.logo_url"
                        :alt="`Logo ${branding.school_name}`"
                        width="36"
                        height="36"
                        decoding="async"
                    >
                </div>
                <h1 id="login-title">{{ branding.school_name }}</h1>
                <div class="product-name">{{ branding.school_short_name }}</div>
                <div class="school-motto">{{ branding.school_motto }}</div>
                <div class="school-address">{{ branding.school_address }}</div>
            </section>

            <section class="login-card" aria-label="Form login">
                <div v-if="flash.error" class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
                    <span>{{ flash.error }}</span>
                </div>
                <div v-if="flash.success" class="alert alert-success d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
                    <span>{{ flash.success }}</span>
                </div>

                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username atau Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person-fill" aria-hidden="true"></i>
                            </span>
                            <input
                                id="username"
                                v-model="form.username"
                                type="text"
                                name="username"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.username }"
                                placeholder="Masukkan username atau email"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        <div v-if="form.errors.username" class="login-error">{{ form.errors.username }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock-fill" aria-hidden="true"></i>
                            </span>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                name="password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                placeholder="Masukkan password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        <div v-if="form.errors.password" class="login-error">{{ form.errors.password }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input
                                id="remember"
                                v-model="form.remember"
                                type="checkbox"
                                name="remember"
                                class="form-check-input"
                            >
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" :disabled="form.processing">
                        <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>
                        {{ form.processing ? 'Memproses...' : 'Masuk' }}
                    </button>
                </form>
            </section>

            <footer class="login-footer">
                &copy; {{ year }} {{ branding.school_name }}<br>
                <span>{{ branding.school_short_name }}</span>
            </footer>
        </div>
    </main>
</template>

<style scoped>
.login-page {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, #166534 0%, #145c32 40%, #0d3625 100%);
    font-family: var(--font-sans);
}

.login-page::before {
    content: '';
    position: fixed;
    inset: -50%;
    background:
        radial-gradient(circle at 30% 20%, rgba(251, 191, 36, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 70% 80%, rgba(34, 197, 94, 0.12) 0%, transparent 50%);
    pointer-events: none;
}

.login-wrapper {
    position: relative;
    width: 100%;
    max-width: 440px;
    animation: fadeInUp 0.5s ease;
}

.login-header {
    text-align: center;
    color: white;
    margin-bottom: 20px;
}

.logo-circle {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    border: 3px solid rgba(251, 191, 36, 0.5);
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 30px rgba(251, 191, 36, 0.15);
}

.logo-circle img {
    width: 36px;
    height: 36px;
    object-fit: contain;
    border-radius: 50%;
}

.login-header h1 {
    font-weight: 800;
    font-size: 1.4rem;
    margin-bottom: 4px;
}

.product-name {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 10px;
    margin-bottom: 8px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.9);
    font-weight: 700;
    font-size: 0.74rem;
}

.school-motto {
    max-width: 360px;
    margin: 0 auto 6px;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.84rem;
    line-height: 1.45;
}

.school-address {
    max-width: 360px;
    margin: 0 auto;
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.74rem;
    line-height: 1.4;
}

.login-card {
    border-radius: 18px;
    background: white;
    padding: 35px 30px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.form-label {
    font-weight: 600;
    font-size: 0.84rem;
    color: #4b5563;
    margin-bottom: 5px;
}

.input-group-text {
    border: 1px solid #e5e7eb;
    border-right: 0;
    border-radius: 10px 0 0 10px;
    background: #f9fafb;
    color: #9ca3af;
}

.form-control {
    border: 1px solid #e5e7eb;
    border-left: 0;
    border-radius: 0 10px 10px 0;
    padding: 12px 16px;
    font-size: 0.92rem;
}

.input-group:focus-within .input-group-text {
    border-color: #22c55e;
    color: #22c55e;
}

.form-control:focus {
    border-color: #22c55e;
    box-shadow: none;
}

.form-check-label {
    color: #6b7280;
    font-size: 0.85rem;
}

.form-check-input:checked {
    border-color: #22c55e;
    background-color: #22c55e;
}

.btn-login {
    width: 100%;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    padding: 14px;
    font: inherit;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.btn-login:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(22, 163, 74, 0.35);
    background: linear-gradient(135deg, #16a34a, #15803d);
}

.btn-login:disabled {
    cursor: wait;
    opacity: 0.75;
}

.alert {
    border: 0;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 0.85rem;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
}

.login-error {
    color: #dc2626;
    font-size: 0.75rem;
    margin-top: 4px;
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.78rem;
}

.login-footer span {
    color: rgba(251, 191, 36, 0.8);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 480px) {
    .login-card {
        padding: 25px 20px;
    }
}
</style>
