<template>
    <div class="admin-feedback space-y-8">
        <Head :title="$t('admin.feedbackTitle')" />

        <section class="hero-panel">
            <div class="hero-glow" aria-hidden="true"></div>
            <div class="hero-content">
                <div>
                    <p class="eyebrow">{{ $t('labels.adminConsole') }}</p>
                    <h1 class="hero-title font-display text-2xl sm:text-3xl">{{ $t('admin.feedbackTitle') }}</h1>
                    <p class="hero-sub">
                        {{ $t('admin.feedbackHeroSub') }}
                    </p>
                </div>
            <div class="hero-stats">
                <div class="stat-card">
                    <p class="stat-label">{{ $t('admin.feedbackTotal') }}</p>
                    <p class="stat-value">{{ totalCount }}</p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">{{ $t('admin.feedbackShown') }}</p>
                    <p class="stat-value">{{ filteredCount }}</p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">{{ $t('admin.feedbackLatest') }}</p>
                    <p class="stat-value text-sm">{{ latestAt }}</p>
                </div>
            </div>
            </div>
            <div v-if="pageLabel" class="page-chip">
                {{ pageLabel }}
            </div>
        </section>

        <div v-if="$page.props.flash.message" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-700">
            {{ $page.props.flash.message }}
        </div>

        <section class="messages-panel">
            <div class="messages-header">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ $t('admin.feedbackMessages') }}</h2>
                    <p class="text-sm text-slate-600">{{ $t('admin.feedbackReplyHint') }}</p>
                </div>
                <div class="search-box">
                    <input
                        v-model="search"
                        type="search"
                        :placeholder="$t('admin.feedbackSearchPlaceholder')"
                        class="search-input"
                    />
                    <button v-if="search" class="clear-btn" type="button" @click="search = ''">
                        {{ $t('admin.feedbackClear') }}
                    </button>
                </div>
            </div>

            <div v-if="pageCount === 0" class="empty-state">
                <div class="empty-icon" aria-hidden="true">MAIL</div>
                <p class="text-lg font-semibold text-slate-800">{{ $t('admin.feedbackEmptyTitle') }}</p>
                <p class="text-sm text-slate-600">{{ $t('admin.feedbackEmptyBody') }}</p>
            </div>
            <div v-else-if="filteredCount === 0" class="empty-state">
                <div class="empty-icon" aria-hidden="true">NOPE</div>
                <p class="text-lg font-semibold text-slate-800">{{ $t('admin.feedbackNoneTitle') }}</p>
                <p class="text-sm text-slate-600">{{ $t('admin.feedbackNoneBody') }}</p>
            </div>

            <div v-else class="space-y-4">
                <article v-for="message in filteredMessages" :key="message.id" class="message-card">
                    <div class="message-meta">
                        <div class="avatar">{{ initials(message.name) }}</div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-slate-900">{{ message.name }}</p>
                                <span class="message-id">#{{ message.id }}</span>
                                <span class="status-pill" :class="message.is_read ? 'is-read' : 'is-unread'">
                                    {{ message.is_read ? $t('admin.feedbackRead') : $t('admin.feedbackUnread') }}
                                </span>
                                <span v-if="message.user_blocked" class="status-pill is-blocked">
                                    {{ $t('admin.feedbackBlocked') }}
                                </span>
                            </div>
                            <a class="message-email" :href="`mailto:${message.email}`">{{ message.email }}</a>
                        </div>
                        <div class="message-date">
                            <p class="text-xs uppercase text-slate-500">{{ $t('admin.feedbackReceived') }}</p>
                            <p class="text-sm font-semibold text-slate-800">{{ message.created_at }}</p>
                        </div>
                    </div>
                    <p class="message-body">{{ message.message }}</p>
                    <div class="message-actions">
                        <a class="reply-btn" :href="`mailto:${message.email}`">{{ $t('admin.feedbackReply') }}</a>
                        <button class="copy-btn" type="button" @click="copyEmail(message.email, message.id)">
                            {{ copiedId === message.id ? $t('admin.feedbackCopied') : $t('admin.feedbackCopyEmail') }}
                        </button>
                        <button class="copy-btn" type="button" @click="message.is_read ? markUnread(message) : markRead(message)">
                            {{ message.is_read ? $t('admin.feedbackMarkUnread') : $t('admin.feedbackMarkRead') }}
                        </button>
                        <button class="copy-btn danger" type="button" @click="blockUser(message)">
                            {{ $t('admin.feedbackBlockUser') }}
                        </button>
                        <button class="copy-btn danger" type="button" @click="deleteMessage(message)">
                            {{ $t('admin.feedbackDelete') }}
                        </button>
                        <span class="hint-text">{{ $t('admin.feedbackReplyHintSmall') }}</span>
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

export default {
    name: 'AdminFeedbackIndex',
    layout: AdminLayout,
    components: {
        Head,
    },
    props: {
        messages: Object,
    },
    data() {
        return {
            search: '',
            copiedId: null,
        };
    },
    computed: {
        totalCount() {
            return this.messages?.total ?? this.messages?.meta?.total ?? this.messages?.data?.length ?? 0;
        },
        pageCount() {
            return this.messages?.data?.length ?? 0;
        },
        filteredMessages() {
            const list = this.messages?.data ?? [];
            const query = this.search.trim().toLowerCase();
            if (!query) {
                return list;
            }
            return list.filter((message) => {
                const haystack = `${message.name} ${message.email} ${message.message}`.toLowerCase();
                return haystack.includes(query);
            });
        },
        filteredCount() {
            return this.filteredMessages.length;
        },
        latestAt() {
            return this.messages?.data?.[0]?.created_at ?? '—';
        },
        pageLabel() {
            const current = this.messages?.current_page ?? this.messages?.meta?.current_page;
            const last = this.messages?.last_page ?? this.messages?.meta?.last_page;
            if (!current || !last) {
                return '';
            }
            return this.$t('admin.feedbackPageLabel', { current, last });
        },
    },
    methods: {
        copyEmail(email, messageId) {
            if (!email || !navigator.clipboard) {
                return;
            }
            navigator.clipboard.writeText(email).then(() => {
                this.copiedId = messageId;
                window.setTimeout(() => {
                    if (this.copiedId === messageId) {
                        this.copiedId = null;
                    }
                }, 1600);
            });
        },
        initials(name) {
            if (!name) {
                return 'NA';
            }
            const parts = name.trim().split(/\s+/).slice(0, 2);
            const letters = parts.map((part) => part[0]).join('');
            return letters.toUpperCase();
        },
        markRead(message) {
            this.$inertia.patch(route('admin.feedback.read', message.id), {}, { preserveScroll: true });
        },
        markUnread(message) {
            this.$inertia.patch(route('admin.feedback.unread', message.id), {}, { preserveScroll: true });
        },
        deleteMessage(message) {
            if (!confirm(this.$t('admin.feedbackDeleteConfirm'))) {
                return;
            }
            this.$inertia.delete(route('admin.feedback.destroy', message.id), { preserveScroll: true });
        },
        blockUser(message) {
            if (message.user_blocked) {
                return;
            }
            if (!confirm(this.$t('admin.feedbackBlockConfirm'))) {
                return;
            }
            this.$inertia.post(route('admin.feedback.block', message.id), {}, { preserveScroll: true });
        },
    },
};
</script>

<style scoped>
.admin-feedback {
    --laravel-red: #ff2d20;
    --excel-green: #217346;
    font-family: 'Space Grotesk', 'Helvetica Neue', sans-serif;
}

.font-display {
    font-family: 'Fraunces', 'Times New Roman', serif;
    letter-spacing: -0.02em;
}

.hero-panel {
    position: relative;
    padding: 2rem;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(15, 23, 42, 0.12);
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
    overflow: hidden;
}

.hero-glow {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(240px 140px at 10% 0%, rgba(255, 45, 32, 0.2), transparent 70%),
        radial-gradient(280px 160px at 90% 0%, rgba(33, 115, 70, 0.22), transparent 70%);
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

@media (min-width: 900px) {
    .hero-content {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.hero-title {
    font-size: clamp(2rem, 4vw, 2.8rem);
    color: #0f172a;
}

.hero-sub {
    margin-top: 0.6rem;
    color: #475569;
}

.eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 600;
}

.hero-stats {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.stat-card {
    padding: 1rem 1.2rem;
    border-radius: 16px;
    background: rgba(248, 250, 252, 0.85);
    border: 1px solid rgba(15, 23, 42, 0.1);
}

.stat-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    color: #64748b;
}

.stat-value {
    margin-top: 0.35rem;
    font-size: 1.4rem;
    font-weight: 700;
    color: #0f172a;
}

.page-chip {
    position: absolute;
    right: 1.5rem;
    bottom: 1.5rem;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    background: rgba(15, 23, 42, 0.08);
    color: #0f172a;
    font-weight: 600;
}

.messages-panel {
    border-radius: 22px;
    padding: 1.6rem;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
}

.messages-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.6rem;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(15, 23, 42, 0.12);
    padding: 0.4rem 0.6rem;
    border-radius: 999px;
}

.search-input {
    border: none;
    outline: none;
    background: transparent;
    width: min(320px, 70vw);
    font-size: 0.9rem;
    color: #0f172a;
}

.clear-btn {
    border: none;
    background: rgba(15, 23, 42, 0.08);
    color: #0f172a;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
}

.empty-state {
    display: grid;
    gap: 0.6rem;
    justify-items: center;
    padding: 2.5rem 1rem;
    text-align: center;
    color: #475569;
}

.empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: rgba(255, 45, 32, 0.1);
    display: grid;
    place-items: center;
    font-size: 1.4rem;
}

.message-card {
    padding: 1.4rem;
    border-radius: 18px;
    background: rgba(248, 250, 252, 0.9);
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 14px 22px rgba(15, 23, 42, 0.08);
}

.message-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
    justify-content: space-between;
}

.avatar {
    width: 46px;
    height: 46px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, var(--laravel-red), var(--excel-green));
}

.message-id {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.08);
    font-size: 0.75rem;
    color: #334155;
}

.message-email {
    font-size: 0.9rem;
    color: #1f5f3a;
    text-decoration: none;
}

.message-email:hover {
    text-decoration: underline;
}

.message-date {
    text-align: right;
}

.message-body {
    margin-top: 1rem;
    color: #475569;
    line-height: 1.6;
}

.message-actions {
    margin-top: 1.2rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: center;
}

.status-pill {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
}

.status-pill.is-read {
    background: rgba(34, 197, 94, 0.15);
    color: #15803d;
}

.status-pill.is-unread {
    background: rgba(249, 115, 22, 0.15);
    color: #c2410c;
}

.status-pill.is-blocked {
    background: rgba(239, 68, 68, 0.15);
    color: #b91c1c;
}

.reply-btn {
    padding: 0.45rem 1rem;
    border-radius: 999px;
    background: linear-gradient(130deg, var(--laravel-red), var(--excel-green));
    color: #ffffff;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 10px 18px rgba(33, 115, 70, 0.2);
}

.copy-btn {
    padding: 0.45rem 1rem;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.14);
    background: rgba(255, 255, 255, 0.8);
    color: #0f172a;
    font-weight: 600;
    cursor: pointer;
}

.copy-btn.danger {
    border-color: rgba(239, 68, 68, 0.4);
    color: #b91c1c;
}

.hint-text {
    font-size: 0.8rem;
    color: #64748b;
}
</style>
