<template>
    <div class="public-shell">
        <Head>
            <link rel="preconnect" href="https://fonts.bunny.net" />
            <link
                href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|fraunces:600,700&display=swap"
                rel="stylesheet"
            />
        </Head>

        <div class="relative min-h-screen overflow-hidden">
            <div class="public-backdrop" aria-hidden="true"></div>
            <div class="public-overlay" aria-hidden="true"></div>

            <header class="relative z-10">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-5">
                    <Link class="brand flex items-center gap-3" :href="route('home')">
                        <span class="brand-mark" aria-hidden="true"></span>
                        <span class="text-lg font-semibold tracking-tight">Excel Import</span>
                    </Link>
                    <nav class="flex flex-wrap items-center gap-2 text-sm">
                        <LanguageSwitcher />
                        <Link class="nav-pill" :href="route('feedback.create')">{{ $t('nav.feedback') }}</Link>
                        <a class="nav-pill" href="/api/documentation" target="_blank" rel="noreferrer">{{ $t('nav.apiDocs') }}</a>
                        <template v-if="$page.props.auth.user">
                            <Link class="nav-pill" :href="route('dashboard')">{{ $t('nav.dashboard') }}</Link>
                        </template>
                        <template v-else>
                            <Link class="nav-pill" :href="route('login')">{{ $t('actions.login') }}</Link>
                            <Link class="nav-cta" :href="route('register')">{{ $t('actions.register') }}</Link>
                        </template>
                    </nav>
                </div>
            </header>

            <main class="relative z-10 mx-auto max-w-6xl px-4 pb-16 pt-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

export default {
    name: 'PublicLayout',

    components: {
        Head,
        Link,
        LanguageSwitcher,
    },
};
</script>

<style scoped>
.public-shell {
    --laravel-red: #ff2d20;
    --excel-green: #217346;
    color: #0f172a;
    font-family: 'Space Grotesk', 'Helvetica Neue', sans-serif;
}

.public-backdrop {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(900px 460px at 10% -10%, rgba(255, 45, 32, 0.22), transparent 70%),
        radial-gradient(860px 420px at 90% 0%, rgba(33, 115, 70, 0.22), transparent 65%),
        url('/images/laravel-excel-bg.svg');
    background-size: auto, auto, cover;
    background-position: center, center, center;
    background-repeat: no-repeat;
    filter: saturate(1.05);
}

.public-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(246, 248, 252, 0.9) 35%, rgba(244, 246, 250, 0.96) 100%);
}

.brand {
    text-decoration: none;
    color: inherit;
}

.brand-mark {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--laravel-red), var(--excel-green));
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.15);
}

.nav-pill {
    padding: 0.45rem 0.85rem;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.12);
    background: rgba(255, 255, 255, 0.7);
    color: #0f172a;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.nav-pill:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
}

.nav-cta {
    padding: 0.5rem 1.05rem;
    border-radius: 999px;
    color: #ffffff;
    text-decoration: none;
    background: linear-gradient(130deg, var(--laravel-red), var(--excel-green));
    box-shadow: 0 12px 22px rgba(33, 115, 70, 0.25);
}
</style>
