<template>
    <div class="space-y-8">
        <Head :title="$t('labels.failedRows')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.validation') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('task.failedRowsTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('task.failedRowsSubtitle') }}</p>
            </div>
            <Link class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700" :href="route('task.index')">
                {{ $t('actions.backToTasks') }}
            </Link>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div v-if="failedList.data.length === 0" class="text-center text-sm text-slate-600">
                {{ $t('task.failedRowsNoErrors') }}
            </div>
            <div v-else class="space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="row in failedList.data" :key="row.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ row.id }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.row') }}</p>
                                <p class="text-sm text-slate-600">{{ row.row }}</p>
                            </div>
                        </div>
                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('task.failedColumn') }}</span>
                                <span class="text-slate-700 text-right">{{ row.key }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.message') }}</span>
                                <span class="text-slate-700 text-right">{{ row.message }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.created') }}</span>
                                <span class="text-slate-700 text-right">{{ row.created_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[640px] text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3 pr-6">ID</th>
                            <th class="pb-3 pr-6">{{ $t('labels.row') }}</th>
                            <th class="pb-3 pr-6">{{ $t('task.failedColumn') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.message') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.task') }}</th>
                            <th class="pb-3">{{ $t('labels.created') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="failedRow in failedList.data" :key="failedRow.id" class="border-t border-slate-200">
                            <td class="py-4 pr-6 font-semibold text-slate-900">#{{ failedRow.id }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ failedRow.row }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ failedRow.key }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ failedRow.message }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ failedRow.task_id }}</td>
                            <td class="py-4 text-slate-600">{{ failedRow.created_at }}</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </section>

        <Pagination :meta="failedList.meta" />
    </div>
</template>


<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'FailedList',
    layout: AdminLayout,
    components: {
        Head,
        Link,
        Pagination,
    },
    props: {
        failedList: Object,
    },
};
</script>
