<template>
    <div class="space-y-8">
        <Head :title="$t('admin.usersTitle')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('admin.usersTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('admin.usersSubtitle') }}</p>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.usersCreateTitle') }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $t('admin.usersCreateSubtitle') }}</p>
                    </div>
                    <button
                        class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                        type="button"
                        @click="openCreate = !openCreate"
                    >
                        {{ openCreate ? $t('actions.cancel') : $t('admin.usersCreateToggle') }}
                    </button>
                </div>

                <div v-if="openCreate" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.name') }}</label>
                        <input
                            v-model="newUser.name"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                            :placeholder="$t('admin.usersNamePlaceholder')"
                        />
                        <div v-if="$page.props.errors?.name" class="mt-1 text-xs text-rose-600">{{ $page.props.errors.name }}</div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.email') }}</label>
                        <input
                            v-model="newUser.email"
                            type="email"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                            placeholder="name@example.com"
                        />
                        <div v-if="$page.props.errors?.email" class="mt-1 text-xs text-rose-600">{{ $page.props.errors.email }}</div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.password') }}</label>
                        <input
                            v-model="newUser.password"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                            :placeholder="$t('admin.usersPasswordPlaceholder')"
                        />
                        <div v-if="$page.props.errors?.password" class="mt-1 text-xs text-rose-600">{{ $page.props.errors.password }}</div>
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-700">
                            <input v-model="newUser.is_admin" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
                            {{ $t('admin.usersMakeAdmin') }}
                        </label>
                    </div>
                </div>

                <div v-if="openCreate" class="mt-4 flex flex-wrap gap-2">
                    <button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white" type="button" @click="createUser">
                        {{ $t('actions.save') }}
                    </button>
                    <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="button" @click="resetCreate">
                        {{ $t('actions.reset') }}
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="search-box">
                    <input
                        v-model="search"
                        type="search"
                        :placeholder="$t('admin.usersSearchPlaceholder')"
                        class="search-input"
                    />
                </div>
                <button
                    class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                    type="button"
                    @click="applyFilters"
                >
                    {{ $t('actions.applyFilters') }}
                </button>
            </div>

            <div v-if="users.data.length === 0" class="mt-6 text-center text-sm text-slate-600">
                {{ $t('admin.usersEmpty') }}
            </div>

            <div v-else class="mt-6 space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="user in users.data" :key="user.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ user.id }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="status-pill" :class="user.is_admin ? 'is-admin' : 'is-user'">
                                    {{ user.is_admin ? $t('admin.usersAdmin') : $t('admin.usersUser') }}
                                </span>
                                <span class="status-pill" :class="user.is_blocked ? 'is-blocked' : 'is-active'">
                                    {{ user.is_blocked ? $t('admin.usersBlocked') : $t('admin.usersActive') }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.name') }}</span>
                                <span class="text-slate-700 text-right">{{ user.name }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.email') }}</span>
                                <span class="text-slate-700 text-right">{{ user.email }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.created') }}</span>
                                <span class="text-slate-700 text-right">{{ user.created_at }}</span>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button
                                class="action-btn"
                                type="button"
                                @click="toggleBlock(user)"
                            >
                                {{ user.is_blocked ? $t('admin.usersUnblock') : $t('admin.usersBlock') }}
                            </button>
                            <button
                                class="action-btn"
                                type="button"
                                @click="toggleAdmin(user)"
                            >
                                {{ user.is_admin ? $t('admin.usersRevoke') : $t('admin.usersMakeAdmin') }}
                            </button>
                            <button class="action-btn danger" type="button" @click="deleteUser(user)">
                                {{ $t('admin.usersDelete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[760px] text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-3 pr-6">ID</th>
                                <th class="pb-3 pr-6">{{ $t('labels.name') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.email') }}</th>
                                <th class="pb-3 pr-6">{{ $t('admin.usersRole') }}</th>
                                <th class="pb-3 pr-6">{{ $t('admin.usersStatus') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.created') }}</th>
                                <th class="pb-3 text-right">{{ $t('labels.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id" class="border-t border-slate-200">
                                <td class="py-4 pr-6 font-semibold text-slate-900">#{{ user.id }}</td>
                                <td class="py-4 pr-6 text-slate-700">{{ user.name }}</td>
                                <td class="py-4 pr-6 text-slate-600">{{ user.email }}</td>
                                <td class="py-4 pr-6">
                                    <span class="status-pill" :class="user.is_admin ? 'is-admin' : 'is-user'">
                                        {{ user.is_admin ? $t('admin.usersAdmin') : $t('admin.usersUser') }}
                                    </span>
                                </td>
                                <td class="py-4 pr-6">
                                    <span class="status-pill" :class="user.is_blocked ? 'is-blocked' : 'is-active'">
                                        {{ user.is_blocked ? $t('admin.usersBlocked') : $t('admin.usersActive') }}
                                    </span>
                                </td>
                                <td class="py-4 pr-6 text-slate-600">{{ user.created_at }}</td>
                                <td class="py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button class="action-btn" type="button" @click="toggleBlock(user)">
                                            {{ user.is_blocked ? $t('admin.usersUnblock') : $t('admin.usersBlock') }}
                                        </button>
                                        <button class="action-btn" type="button" @click="toggleAdmin(user)">
                                            {{ user.is_admin ? $t('admin.usersRevoke') : $t('admin.usersMakeAdmin') }}
                                        </button>
                                        <button class="action-btn danger" type="button" @click="deleteUser(user)">
                                            {{ $t('admin.usersDelete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <Pagination :meta="users.meta" />
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'AdminUsersIndex',
    layout: AdminLayout,
    components: {
        Head,
        Pagination,
    },
    props: {
        users: Object,
        filters: Object,
    },
    data() {
        return {
            search: this.filters?.q ?? '',
            openCreate: false,
            newUser: {
                name: '',
                email: '',
                password: '',
                is_admin: false,
            },
        };
    },
    methods: {
        applyFilters() {
            this.$inertia.get(route('admin.users.index'), {
                q: this.search || undefined,
            }, {
                preserveState: true,
                replace: true,
            });
        },
        toggleBlock(user) {
            const routeName = user.is_blocked ? 'admin.users.unblock' : 'admin.users.block';
            this.$inertia.patch(route(routeName, user.id), {}, { preserveScroll: true });
        },
        toggleAdmin(user) {
            const routeName = user.is_admin ? 'admin.users.revoke_admin' : 'admin.users.make_admin';
            this.$inertia.patch(route(routeName, user.id), {}, { preserveScroll: true });
        },
        deleteUser(user) {
            if (!confirm(this.$t('admin.usersDeleteConfirm'))) {
                return;
            }
            this.$inertia.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
        },
        createUser() {
            this.$inertia.post(route('admin.users.store'), this.newUser, {
                preserveScroll: true,
                onSuccess: () => {
                    this.resetCreate();
                    this.openCreate = false;
                },
            });
        },
        resetCreate() {
            this.newUser = {
                name: '',
                email: '',
                password: '',
                is_admin: false,
            };
        },
    },
};
</script>

<style scoped>
.search-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(248, 250, 252, 0.9);
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    border: 1px solid rgba(15, 23, 42, 0.12);
}

.search-input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 220px;
}

.action-btn {
    border: 1px solid rgba(15, 23, 42, 0.12);
    border-radius: 999px;
    padding: 0.35rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 600;
    background: #ffffff;
    color: #0f172a;
    cursor: pointer;
}

.action-btn.danger {
    border-color: rgba(239, 68, 68, 0.4);
    color: #b91c1c;
}

.status-pill {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
}

.status-pill.is-admin {
    background: rgba(59, 130, 246, 0.15);
    color: #1d4ed8;
}

.status-pill.is-user {
    background: rgba(148, 163, 184, 0.2);
    color: #475569;
}

.status-pill.is-active {
    background: rgba(34, 197, 94, 0.15);
    color: #15803d;
}

.status-pill.is-blocked {
    background: rgba(239, 68, 68, 0.15);
    color: #b91c1c;
}
</style>
