<template>
    <div class="space-y-16">
        <Head :title="$t('labels.home')" />
        <section class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
            <div class="space-y-6">
                <div class="hero-tag rise rise-1">{{ $t('labels.homeTag') }}</div>
                <h1 class="hero-title font-display rise rise-2">
                    {{ $t('home.heroTitle') }}
                </h1>
                <p class="text-lg text-slate-700 rise rise-3">
                    {{ $t('home.heroBody') }}
                </p>
                <div class="flex flex-wrap gap-3 rise rise-4">
                    <template v-if="$page.props.auth.user">
                        <Link class="btn-primary" :href="route('project.import')">{{ $t('actions.startImport') }}</Link>
                        <Link class="btn-ghost" :href="route('dashboard')">{{ $t('actions.openDashboard') }}</Link>
                    </template>
                    <template v-else>
                        <Link class="btn-primary" :href="route('register')">{{ $t('actions.createAccount') }}</Link>
                        <Link class="btn-ghost" :href="route('login')">{{ $t('actions.login') }}</Link>
                    </template>
                    <a class="btn-outline" href="/api/documentation" target="_blank" rel="noreferrer">
                        {{ $t('nav.apiDocs') }}
                    </a>
                </div>
                <div class="grid gap-4 sm:grid-cols-3 rise rise-5">
                    <div class="stat-card">
                        <p class="stat-title">{{ $t('home.queueFirstTitle') }}</p>
                        <p class="stat-body">{{ $t('home.queueFirstBody') }}</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-title">{{ $t('home.rowQaTitle') }}</p>
                        <p class="stat-body">{{ $t('home.rowQaBody') }}</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-title">{{ $t('home.swaggerTitle') }}</p>
                        <p class="stat-body">{{ $t('home.swaggerBody') }}</p>
                    </div>
                </div>
            </div>

            <div class="panel rise rise-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $t('home.liveImport') }}</p>
                        <h2 class="text-2xl font-semibold text-slate-900">{{ $t('home.liveFile') }}</h2>
                    </div>
                    <span class="badge">{{ $t('labels.inQueue') }}</span>
                </div>
                <div class="mt-6 space-y-4">
                    <div class="status-row">
                        <span class="status-dot dot-green"></span>
                        <div>
                            <p class="status-title">{{ $t('home.rowsProcessed') }}</p>
                            <p class="status-sub">{{ $t('home.rowsProcessedBody') }}</p>
                        </div>
                    </div>
                    <div class="status-row">
                        <span class="status-dot dot-amber"></span>
                        <div>
                            <p class="status-title">{{ $t('home.rowsFlagged') }}</p>
                            <p class="status-sub">{{ $t('home.rowsFlaggedBody') }}</p>
                        </div>
                    </div>
                    <div class="status-row">
                        <span class="status-dot dot-red"></span>
                        <div>
                            <p class="status-title">{{ $t('home.rowsFailed') }}</p>
                            <p class="status-sub">{{ $t('home.rowsFailedBody') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                    <div class="mini-card">
                        <p class="text-slate-500">{{ $t('labels.taskStatus') }}</p>
                        <p class="font-semibold text-slate-900">{{ $t('labels.processing') }}</p>
                    </div>
                    <div class="mini-card">
                        <p class="text-slate-500">{{ $t('labels.nextUp') }}</p>
                        <p class="font-semibold text-slate-900">{{ $t('home.nextUp') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="info-card rise rise-1">
                <p class="info-eyebrow">{{ $t('home.importSafety') }}</p>
                <h3 class="info-title font-display">{{ $t('home.importSafetyTitle') }}</h3>
                <p class="info-body">{{ $t('home.importSafetyBody') }}</p>
            </div>
            <div class="info-card rise rise-2">
                <p class="info-eyebrow">{{ $t('home.operations') }}</p>
                <h3 class="info-title font-display">{{ $t('home.operationsTitle') }}</h3>
                <p class="info-body">{{ $t('home.operationsBody') }}</p>
            </div>
            <div class="info-card rise rise-3">
                <p class="info-eyebrow">{{ $t('home.visibility') }}</p>
                <h3 class="info-title font-display">{{ $t('home.visibilityTitle') }}</h3>
                <p class="info-body">{{ $t('home.visibilityBody') }}</p>
            </div>
        </section>

        <section class="flow-grid">
            <div class="flow-card rise rise-1">
                <span class="flow-step">01</span>
                <h3 class="flow-title font-display">{{ $t('home.flowStep1Title') }}</h3>
                <p class="flow-body">{{ $t('home.flowStep1Body') }}</p>
            </div>
            <div class="flow-card rise rise-2">
                <span class="flow-step">02</span>
                <h3 class="flow-title font-display">{{ $t('home.flowStep2Title') }}</h3>
                <p class="flow-body">{{ $t('home.flowStep2Body') }}</p>
            </div>
            <div class="flow-card rise rise-3">
                <span class="flow-step">03</span>
                <h3 class="flow-title font-display">{{ $t('home.flowStep3Title') }}</h3>
                <p class="flow-body">{{ $t('home.flowStep3Body') }}</p>
            </div>
        </section>

        <section class="cta-panel rise rise-2">
            <div>
                <p class="cta-eyebrow">{{ $t('home.ctaEyebrow') }}</p>
                <h2 class="cta-title font-display">{{ $t('home.ctaTitle') }}</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <template v-if="$page.props.auth.user">
                    <Link class="btn-primary" :href="route('project.import')">{{ $t('actions.startImport') }}</Link>
                </template>
                <template v-else>
                    <Link class="btn-primary" :href="route('register')">{{ $t('actions.createAccount') }}</Link>
                </template>
                <Link class="btn-outline" :href="route('feedback.create')">{{ $t('actions.sendFeedback') }}</Link>
            </div>
        </section>
    </div>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'Home',
    layout: PublicLayout,
    components: {
        Head,
        Link,
    },
};
</script>

<style scoped>
.font-display {
    font-family: 'Fraunces', 'Times New Roman', serif;
    letter-spacing: -0.02em;
}

.hero-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.9rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    background: rgba(15, 23, 42, 0.06);
}

.hero-title {
    font-size: clamp(2.4rem, 4vw, 3.6rem);
    line-height: 1.05;
    color: #0f172a;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1.2rem;
    border-radius: 999px;
    color: #ffffff;
    background: linear-gradient(130deg, var(--laravel-red), var(--excel-green));
    box-shadow: 0 14px 24px rgba(33, 115, 70, 0.25);
    text-decoration: none;
    font-weight: 600;
}

.btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.6rem 1.1rem;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.15);
    background: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    color: #0f172a;
    font-weight: 600;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.6rem 1.1rem;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.2);
    text-decoration: none;
    color: #0f172a;
    background: rgba(255, 255, 255, 0.4);
    font-weight: 600;
}

.stat-card {
    padding: 1.1rem;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
}

.stat-title {
    font-weight: 700;
    color: #0f172a;
}

.stat-body {
    margin-top: 0.35rem;
    color: #475569;
    font-size: 0.9rem;
}

.panel {
    border-radius: 24px;
    padding: 1.6rem;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid rgba(15, 23, 42, 0.12);
    box-shadow: 0 24px 40px rgba(15, 23, 42, 0.12);
}

.badge {
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    background: rgba(33, 115, 70, 0.12);
    color: #1f5f3a;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.status-row {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    margin-top: 0.35rem;
}

.dot-green {
    background: #1f5f3a;
}

.dot-amber {
    background: #f59e0b;
}

.dot-red {
    background: #ef4444;
}

.status-title {
    font-weight: 600;
    color: #0f172a;
}

.status-sub {
    font-size: 0.9rem;
    color: #64748b;
}

.mini-card {
    padding: 0.75rem 0.9rem;
    border-radius: 14px;
    background: rgba(248, 250, 252, 0.8);
    border: 1px solid rgba(15, 23, 42, 0.08);
}

.info-card {
    padding: 1.4rem;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(15, 23, 42, 0.1);
    box-shadow: 0 20px 32px rgba(15, 23, 42, 0.08);
}

.info-eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
}

.info-title {
    margin-top: 0.5rem;
    font-size: 1.5rem;
    color: #0f172a;
}

.info-body {
    margin-top: 0.6rem;
    color: #475569;
}

.flow-grid {
    display: grid;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .flow-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.flow-card {
    padding: 1.4rem;
    border-radius: 20px;
    background: rgba(15, 23, 42, 0.85);
    color: #f8fafc;
    box-shadow: 0 20px 30px rgba(15, 23, 42, 0.2);
}

.flow-step {
    display: inline-block;
    font-size: 0.75rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(248, 250, 252, 0.6);
}

.flow-title {
    margin-top: 0.6rem;
    font-size: 1.4rem;
    color: #f8fafc;
}

.flow-body {
    margin-top: 0.6rem;
    color: rgba(248, 250, 252, 0.75);
}

.cta-panel {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 2rem;
    border-radius: 26px;
    background: linear-gradient(140deg, rgba(255, 45, 32, 0.14), rgba(33, 115, 70, 0.12));
    border: 1px solid rgba(15, 23, 42, 0.12);
}

@media (min-width: 768px) {
    .cta-panel {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.cta-eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
}

.cta-title {
    margin-top: 0.6rem;
    font-size: 2rem;
    color: #0f172a;
}

.rise {
    opacity: 0;
    animation: rise 0.7s ease forwards;
}

.rise-1 {
    animation-delay: 0.1s;
}

.rise-2 {
    animation-delay: 0.2s;
}

.rise-3 {
    animation-delay: 0.3s;
}

.rise-4 {
    animation-delay: 0.4s;
}

.rise-5 {
    animation-delay: 0.5s;
}

@keyframes rise {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
