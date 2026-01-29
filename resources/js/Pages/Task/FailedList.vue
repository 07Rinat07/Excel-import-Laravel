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
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs">
                    <div class="text-slate-500">
                        {{ correctedCount }} {{ $t('task.correctedRows') }} / {{ failedList.data.length }}
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="rounded-full border border-slate-300 px-3 py-1 text-[0.7rem] font-semibold text-slate-700"
                            @click="saveAll"
                        >
                            {{ $t('task.saveAllCorrections') }}
                        </button>
                        <button
                            type="button"
                            class="rounded-full bg-slate-900 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-white"
                            @click="reimportCorrected"
                        >
                            {{ $t('task.reimportCorrected') }}
                        </button>
                    </div>
                </div>
                <div class="space-y-4 sm:hidden">
                    <div v-for="row in rows" :key="row.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ row.id }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.row') }}</p>
                                <p class="text-sm text-slate-600">{{ row.row_number ?? row.row }}</p>
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
                        <div class="mt-4 space-y-2">
                            <div
                                v-for="column in templateColumns"
                                :key="`${row.id}-${column.key}`"
                                class="flex flex-col gap-2"
                            >
                                <label class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ column.label }}</label>
                                <input
                                    v-model="row.editData[column.key]"
                                    type="text"
                                    class="rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700"
                                />
                                <div v-if="row.errors && row.errors[column.key]" class="text-[0.65rem] text-rose-600">
                                    {{ formatErrors(row.errors[column.key]) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="rounded-full border border-slate-300 px-3 py-1 text-[0.7rem] font-semibold text-slate-700"
                                @click="saveRow(row)"
                            >
                                {{ $t('actions.save') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <div class="space-y-4">
                        <div v-for="row in rows" :key="row.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-900">#{{ row.id }}</div>
                                <div class="text-xs text-slate-500">{{ $t('labels.row') }}: {{ row.row_number ?? row.row }}</div>
                            </div>
                            <div class="mt-2 text-xs text-slate-600">
                                {{ row.message }}
                            </div>
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-[720px] text-xs">
                                    <thead>
                                        <tr class="text-left text-slate-500">
                                            <th v-for="column in templateColumns" :key="`header-${column.key}`" class="pb-2 pr-4">
                                                {{ column.label }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td v-for="column in templateColumns" :key="`cell-${row.id}-${column.key}`" class="pr-4">
                                                <input
                                                    v-model="row.editData[column.key]"
                                                    type="text"
                                                    class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs"
                                                />
                                                <div v-if="row.errors && row.errors[column.key]" class="mt-1 text-[0.65rem] text-rose-600">
                                                    {{ formatErrors(row.errors[column.key]) }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    class="rounded-full border border-slate-300 px-3 py-1 text-[0.7rem] font-semibold text-slate-700"
                                    @click="saveRow(row)"
                                >
                                    {{ $t('actions.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
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
        taskId: Number,
        templateColumns: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            rows: [],
        };
    },
    computed: {
        correctedCount() {
            return this.rows.filter((row) => row.is_corrected).length;
        },
    },
    mounted() {
        this.rows = this.failedList.data.map((row) => {
            const baseData = row.corrected_data || row.data || {};
            return {
                ...row,
                editData: { ...baseData },
            };
        });
    },
    methods: {
        formatErrors(value) {
            if (Array.isArray(value)) {
                return value.join(', ');
            }
            if (typeof value === 'string') {
                return value;
            }
            return '';
        },
        async saveRow(row) {
            try {
                const response = await window.axios.patch(
                    route('api.failed_rows.update', row.id),
                    { corrected_data: row.editData }
                );
                const data = response.data?.data || response.data;
                row.is_corrected = true;
                row.corrected_data = data?.corrected_data ?? row.editData;
                row.errors = data?.errors ?? {};
            } catch (error) {
                const errors = error?.response?.data?.errors ?? {};
                row.errors = errors;
            }
        },
        async saveAll() {
            const payload = this.rows.map((row) => ({
                id: row.id,
                corrected_data: row.editData,
            }));

            try {
                const response = await window.axios.post(
                    route('api.failed_rows.bulk_update', { task: this.taskId }),
                    { rows: payload }
                );
                const failed = response.data?.failed ?? [];
                const failedMap = new Map(failed.map((item) => [item.id, item.errors]));

                this.rows.forEach((row) => {
                    if (failedMap.has(row.id)) {
                        row.errors = failedMap.get(row.id);
                    } else {
                        row.is_corrected = true;
                        row.errors = {};
                    }
                });
            } catch (error) {
                // Leave per-row errors as-is
            }
        },
        async reimportCorrected() {
            try {
                await window.axios.post(
                    route('api.failed_rows.reimport', { task: this.taskId })
                );
            } catch (error) {
                // No-op: keep UI state
            }
        },
    },
};
</script>
