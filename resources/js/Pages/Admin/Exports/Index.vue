<template>
    <div class="space-y-8">
        <Head :title="$t('admin.exportsTitle')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('admin.exportsTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('admin.exportsSubtitle') }}</p>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div v-if="exports.data.length === 0" class="text-center text-sm text-slate-600">
                {{ $t('admin.exportsEmpty') }}
            </div>
            <div v-else class="space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="item in exports.data" :key="item.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ item.id }}</p>
                            </div>
                            <span class="status-pill" :class="item.status === 'success' ? 'is-success' : 'is-failed'">
                                {{ item.status === 'success' ? $t('admin.exportsSuccess') : $t('admin.exportsFailed') }}
                            </span>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.exportsFrom') }}</span>
                                <span class="text-slate-700 text-right">{{ formatSource(item) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.exportsTo') }}</span>
                                <span class="text-slate-700 text-right">{{ item.file_name }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.user') }}</span>
                                <span class="text-slate-700 text-right">{{ item.user?.name || '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.created') }}</span>
                                <span class="text-slate-700 text-right">{{ item.created_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[720px] text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-3 pr-6">ID</th>
                                <th class="pb-3 pr-6">{{ $t('admin.exportsFrom') }}</th>
                                <th class="pb-3 pr-6">{{ $t('admin.exportsTo') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.user') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.status') }}</th>
                                <th class="pb-3">{{ $t('labels.created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in exports.data" :key="item.id" class="border-t border-slate-200">
                                <td class="py-4 pr-6 font-semibold text-slate-900">#{{ item.id }}</td>
                                <td class="py-4 pr-6 text-slate-700">{{ formatSource(item) }}</td>
                                <td class="py-4 pr-6 text-slate-600">{{ item.file_name }}</td>
                                <td class="py-4 pr-6 text-slate-600">{{ item.user?.name || '—' }}</td>
                                <td class="py-4 pr-6">
                                    <span class="status-pill" :class="item.status === 'success' ? 'is-success' : 'is-failed'">
                                        {{ item.status === 'success' ? $t('admin.exportsSuccess') : $t('admin.exportsFailed') }}
                                    </span>
                                </td>
                                <td class="py-4 text-slate-600">{{ item.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <Pagination :meta="exports.meta" />
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'AdminExportsIndex',
    layout: AdminLayout,
    components: {
        Head,
        Pagination,
    },
    props: {
        exports: Object,
    },
    methods: {
        formatSource(item) {
            if (!item.source_type) {
                return '—';
            }
            return `${item.source_type} #${item.source_id ?? '—'}`;
        },
    },
};
</script>

<style scoped>
.status-pill {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
}

.status-pill.is-success {
    background: rgba(34, 197, 94, 0.15);
    color: #15803d;
}

.status-pill.is-failed {
    background: rgba(239, 68, 68, 0.15);
    color: #b91c1c;
}
</style>
