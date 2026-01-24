<template>
    <div class="admin-shell">
        <Head>
            <link rel="preconnect" href="https://fonts.bunny.net" />
            <link
                href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|fraunces:600,700&display=swap"
                rel="stylesheet"
            />
        </Head>

        <div class="admin-bg" aria-hidden="true"></div>

        <header class="admin-top">
            <div class="brand">
                <span class="brand-dot" aria-hidden="true"></span>
                <div>
                    <p class="brand-eyebrow">{{ $t('labels.adminConsole') }}</p>
                    <p class="brand-title">Excel Import</p>
                </div>
            </div>
            <div class="admin-actions">
                <LanguageSwitcher />
                <Link class="action ghost" :href="route('dashboard')">{{ $t('actions.backToDashboard') }}</Link>
                <Link class="action ghost" :href="route('home')">{{ $t('nav.publicSite') }}</Link>
                <Link class="action" :href="route('project.import')">{{ $t('actions.newImport') }}</Link>
                <Link class="action danger" method="post" as="button" :href="route('logout')">
                    {{ $t('actions.logout') }}
                </Link>
            </div>
        </header>

        <div class="admin-layout">
            <aside class="admin-nav">
                <p class="nav-title">{{ $t('nav.navigation') }}</p>
                <Link :class="navLinkClass('Project/Index')" :href="route('project.index')">
                    {{ $t('nav.projects') }}
                </Link>
                <Link :class="navLinkClass('Project/Import')" :href="route('project.import')">
                    {{ $t('nav.imports') }}
                </Link>
                <Link :class="navLinkClass(['Task/Index', 'Task/FailedList'])" :href="route('task.index')">
                    {{ $t('nav.tasks') }}
                </Link>
                <Link
                    v-if="$page.props.auth.user && $page.props.auth.user.is_admin"
                    :class="navLinkClass(['Admin/Exports/Index'])"
                    :href="route('admin.exports.index')"
                >
                    {{ $t('nav.exports') }}
                </Link>
                <Link
                    v-if="$page.props.auth.user && $page.props.auth.user.is_admin"
                    :class="navLinkClass(['Admin/Users/Index'])"
                    :href="route('admin.users.index')"
                >
                    {{ $t('nav.users') }}
                </Link>
                <Link
                    v-if="$page.props.auth.user && $page.props.auth.user.is_admin"
                    :class="navLinkClass(['Admin/Feedback/Index'])"
                    :href="route('admin.feedback.index')"
                >
                    {{ $t('nav.feedbackInbox') }}
                </Link>
                <Link
                    v-if="$page.props.auth.user && $page.props.auth.user.is_admin"
                    :class="navLinkClass(['Admin/Templates/Index', 'Admin/Templates/Edit'])"
                    :href="route('admin.templates.index')"
                >
                    {{ $t('nav.templates') }}
                </Link>
            </aside>

            <main class="admin-main">
                <div class="admin-card">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

export default {
    name: 'AdminLayout',
    components: {
        Head,
        Link,
        LanguageSwitcher,
    },
    methods: {
        navLinkClass(match) {
            const current = this.$page.component;
            const active = Array.isArray(match) ? match.includes(current) : current === match;
            return ['nav-link', active ? 'is-active' : ''];
        },
    },
};
</script>

<style scoped>
.admin-shell {
    position: relative;
    min-height: 100vh;
    font-family: 'Space Grotesk', 'Helvetica Neue', sans-serif;
    color: #0f172a;
    padding: 2rem clamp(1rem, 4vw, 2.5rem) 3rem;
}

.admin-bg {
    position: fixed;
    inset: 0;
    background:
        radial-gradient(400px 240px at 5% 0%, rgba(255, 45, 32, 0.2), transparent 70%),
        radial-gradient(460px 260px at 95% 0%, rgba(33, 115, 70, 0.22), transparent 70%),
        linear-gradient(180deg, #f7f8fb 0%, #eef1f6 100%);
    z-index: 0;
}

.admin-top {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 2rem;
}

.brand {
    display: flex;
    align-items: center;
    gap: 0.9rem;
}

.brand-dot {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: linear-gradient(130deg, #ff2d20, #217346);
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
}

.brand-eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 600;
}

.brand-title {
    font-family: 'Fraunces', 'Times New Roman', serif;
    font-size: 1.4rem;
    letter-spacing: -0.02em;
}

.admin-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
}

@media (max-width: 640px) {
    .admin-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .action {
        flex: 1 1 auto;
    }
}

.action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.55rem 1rem;
    border-radius: 999px;
    background: linear-gradient(130deg, #ff2d20, #217346);
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 10px 20px rgba(33, 115, 70, 0.22);
}

.action.ghost {
    background: rgba(255, 255, 255, 0.9);
    color: #0f172a;
    border: 1px solid rgba(15, 23, 42, 0.12);
    box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
}

.action.danger {
    background: #0f172a;
    color: #ffffff;
}

.admin-layout {
    position: relative;
    z-index: 1;
    display: grid;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .admin-layout {
        grid-template-columns: 240px minmax(0, 1fr);
        align-items: start;
    }
}

.admin-nav {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 1.4rem;
    border: 1px solid rgba(15, 23, 42, 0.1);
    box-shadow: 0 16px 24px rgba(15, 23, 42, 0.08);
    display: grid;
    gap: 0.6rem;
}

.nav-title {
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.4rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.6rem 0.9rem;
    border-radius: 12px;
    text-decoration: none;
    color: #0f172a;
    font-weight: 600;
    background: rgba(248, 250, 252, 0.8);
    border: 1px solid rgba(15, 23, 42, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.nav-link:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 16px rgba(15, 23, 42, 0.1);
}

.nav-link.is-active {
    background: linear-gradient(130deg, #ff2d20, #217346);
    color: #ffffff;
    border: none;
    box-shadow: 0 14px 22px rgba(33, 115, 70, 0.22);
}

.admin-main {
    min-height: 60vh;
}

.admin-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    padding: clamp(1.5rem, 3vw, 2.4rem);
    border: 1px solid rgba(15, 23, 42, 0.1);
    box-shadow: 0 20px 32px rgba(15, 23, 42, 0.1);
}
</style>
