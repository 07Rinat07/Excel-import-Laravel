<template>
    <div class="space-y-8">
        <Head :title="$t('labels.importTasks')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.importsEyebrow') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('labels.importTasks') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('task.subtitle') }}</p>
            </div>
            <Link class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700" :href="route('project.import')">
                {{ $t('actions.newImport') }}
            </Link>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="mb-4 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                <span class="uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.progressLegend') }}</span>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $t('labels.success') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    <span>{{ $t('labels.failed') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    <span>{{ $t('labels.processing') }}</span>
                </div>
            </div>
            <div v-if="tasks.data.length === 0" class="text-center text-sm text-slate-600">
                {{ $t('labels.noTasks') }}
            </div>
            <div v-else class="space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="task in tasks.data" :key="task.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ task.id }}</p>
                            </div>
                            <span class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ task.status }}
                            </span>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.file') }}</span>
                                <span class="text-slate-700 text-right">{{ task.file.title }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.type') }}</span>
                                <span class="text-slate-700 text-right">{{ task.type?.title || '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.failed') }}</span>
                                <span class="text-slate-700 text-right">{{ task.failed_rows_count }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.stats') }}</span>
                                <span class="text-slate-700 text-right">
                                    {{ task.imported_rows ?? 0 }} / {{ task.total_rows ?? 0 }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-200">
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :class="progressClass(task)"
                                            :title="progressTooltip(task)"
                                            :style="{ width: `${progressPercent(task)}%` }"
                                        ></div>
                                    </div>
                                    <span
                                        class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 text-[0.65rem] font-semibold text-slate-500"
                                        :title="progressTooltip(task)"
                                    >
                                        i
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('task.export', { task: task.id, format: 'xlsx' })">
                                XLSX
                            </Link>
                            <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('task.export', { task: task.id, format: 'csv' })">
                                CSV
                            </Link>
                            <Link
                                v-if="task.failed_rows_count > 0"
                                class="rounded-full border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600"
                                :href="route('task.failed_list', task.id)"
                            >
                                {{ $t('actions.viewFailedRows') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[640px] text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3 pr-6">ID</th>
                            <th class="pb-3 pr-6">{{ $t('labels.user') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.file') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.type') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.status') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.failed') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.stats') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.importSummary') }}</th>
                            <th class="pb-3 text-right">{{ $t('labels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks.data" :key="task.id" class="border-t border-slate-200">
                            <td class="py-4 pr-6 font-semibold text-slate-900">#{{ task.id }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ task.user.name }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ task.file.title }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ task.type?.title || '—' }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ task.status }}</td>
                            <td class="py-4 pr-6 text-slate-600">
                                <Link
                                    v-if="task.failed_rows_count > 0"
                                    class="rounded-full border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600"
                                    :href="route('task.failed_list', task.id)"
                                >
                                    {{ task.failed_rows_count }} {{ $t('labels.errors') }}
                                </Link>
                                <span v-else>0</span>
                            </td>
                            <td class="py-4 pr-6 text-slate-600">
                                {{ task.imported_rows ?? 0 }} / {{ task.total_rows ?? 0 }}
                            </td>
                            <td class="py-4 pr-6 text-slate-600">
                                <div class="flex flex-col gap-1">
                                    <span>
                                        {{ task.imported_rows ?? 0 }} / {{ task.total_rows ?? 0 }} / {{ task.failed_rows_count }}
                                    </span>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :class="progressClass(task)"
                                            :title="progressTooltip(task)"
                                            :style="{ width: `${progressPercent(task)}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('task.export', { task: task.id, format: 'xlsx' })">
                                        XLSX
                                    </Link>
                                    <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('task.export', { task: task.id, format: 'csv' })">
                                        CSV
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </section>

        <Pagination :meta="tasks.meta" />
    </div>
</template>


<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'TaskIndex',
    layout: AdminLayout,
    components: {
        Head,
        Link,
        Pagination,
    },
    props: {
        tasks: Object,
    },
    methods: {
        progressPercent(task) {
            const total = Number(task.total_rows ?? 0);
            const imported = Number(task.imported_rows ?? 0);
            if (total <= 0) {
                return 0;
            }
            return Math.min(100, Math.round((imported / total) * 100));
        },
        progressClass(task) {
            const status = Number(task.status_code ?? 0);
            if (status === 2) {
                return 'bg-emerald-500';
            }
            if (status === 3) {
                return 'bg-rose-500';
            }
            return 'bg-amber-400';
        },
        progressTooltip(task) {
            const total = Number(task.total_rows ?? 0);
            const imported = Number(task.imported_rows ?? 0);
            const failed = Number(task.failed_rows_count ?? 0);
            const percent = total > 0 ? Math.min(100, Math.round((imported / total) * 100)) : 0;
            const status = Number(task.status_code ?? 0);
            let statusLabel = this.$t('labels.processing');
            if (status === 2) {
                statusLabel = this.$t('labels.success');
            } else if (status === 3) {
                statusLabel = this.$t('labels.failed');
            }
            return this.$t('labels.progressTooltip', {
                imported,
                total,
                failed,
                percent,
                status: statusLabel,
            });
        },
    },
};
</script>
