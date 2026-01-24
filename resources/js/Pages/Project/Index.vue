<template>
    <div class="space-y-8">
        <Head :title="$t('labels.importedRecords')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.dataHub') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('labels.importedRecords') }}</h1>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $t('project.filtersTitle') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Link class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700" :href="route('project.import')">
                    {{ $t('actions.newImport') }}
                </Link>
                <Link
                    v-if="form.task_id"
                    class="w-full sm:w-auto rounded-full bg-slate-900 px-4 py-2 text-center text-xs font-semibold text-white"
                    :href="route('task.export', { task: form.task_id, format: 'xlsx' })"
                >
                    {{ $t('labels.exportTaskXlsx') }}
                </Link>
                <Link
                    v-if="form.task_id"
                    class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700"
                    :href="route('task.export', { task: form.task_id, format: 'csv' })"
                >
                    {{ $t('labels.exportTaskCsv') }}
                </Link>
                <Link
                    v-if="form.type_id"
                    class="w-full sm:w-auto rounded-full bg-slate-900 px-4 py-2 text-center text-xs font-semibold text-white"
                    :href="route('type.export', { type: form.type_id, format: 'xlsx' })"
                >
                    {{ $t('labels.exportTypeXlsx') }}
                </Link>
                <Link
                    v-if="form.type_id"
                    class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700"
                    :href="route('type.export', { type: form.type_id, format: 'csv' })"
                >
                    {{ $t('labels.exportTypeCsv') }}
                </Link>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <form class="grid gap-4 lg:grid-cols-4" @submit.prevent="applyFilters">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.type') }}</label>
                    <select v-model="form.type_id" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">—</option>
                        <option v-for="type in types" :key="type.id" :value="type.id">
                            {{ type.title }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.task') }}</label>
                    <select v-model="form.task_id" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">—</option>
                        <option v-for="task in tasks" :key="task.id" :value="task.id">
                            #{{ task.id }} — {{ task.label }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.template') }}</label>
                    <select v-model="form.template_id" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">—</option>
                        <option v-for="template in templates" :key="template.id" :value="template.id">
                            {{ template.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.search') }}</label>
                    <input v-model="form.q" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" type="search" :placeholder="$t('labels.searchPlaceholder')" />
                </div>

                <div v-if="columns.length" class="lg:col-span-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.filters') }}</p>
                    <div class="mt-3 grid gap-3 lg:grid-cols-3">
                        <div v-for="column in columns" :key="column.key">
                            <label class="text-xs font-medium text-slate-600">{{ column.label }}</label>
                            <input
                                v-model="form.columns[column.key]"
                                class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                type="text"
                                :placeholder="$t('labels.filterPlaceholder', { label: column.label })"
                            />
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-wrap gap-2">
                    <button class="w-full sm:w-auto rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white" type="submit">
                        {{ $t('actions.applyFilters') }}
                    </button>
                    <button class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="button" @click="resetFilters">
                        {{ $t('actions.resetFilters') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div v-if="projects.data.length === 0" class="text-center text-sm text-slate-600">
                {{ $t('project.noRecords') }}
            </div>
            <div v-else class="space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="project in projects.data" :key="project.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ project.id }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('project.tableRow') }}</p>
                                <p class="text-sm text-slate-600">{{ project.row_index || '—' }}</p>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('project.tableTitle') }}</span>
                                <span class="text-slate-700 text-right">{{ project.title || '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('project.tableType') }}</span>
                                <span class="text-slate-700 text-right">{{ project.type?.title || '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('project.tableTask') }}</span>
                                <span class="text-slate-700 text-right">{{ project.task_id || '—' }}</span>
                            </div>
                        </div>

                        <div v-if="columns.length" class="mt-4 space-y-2">
                            <div v-for="column in columns" :key="column.key" class="flex items-start justify-between gap-3 text-xs">
                                <span class="text-slate-500">{{ column.label }}</span>
                                <span class="text-slate-800 text-right">{{ project.values?.[column.key] ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('project.export', { project: project.id, format: 'xlsx' })">
                                XLSX
                            </Link>
                            <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('project.export', { project: project.id, format: 'csv' })">
                                CSV
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[720px] text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3 pr-6">ID</th>
                            <th class="pb-3 pr-6">{{ $t('project.tableRow') }}</th>
                            <th class="pb-3 pr-6">{{ $t('project.tableType') }}</th>
                            <th class="pb-3 pr-6">{{ $t('project.tableTask') }}</th>
                            <th class="pb-3 pr-6">{{ $t('project.tableTitle') }}</th>
                            <th v-for="column in columns" :key="column.key" class="pb-3 pr-6">
                                {{ column.label }}
                            </th>
                            <th class="pb-3 text-right">{{ $t('project.tableExport') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="project in projects.data" :key="project.id" class="border-t border-slate-200">
                            <td class="py-4 pr-6 font-semibold text-slate-900">#{{ project.id }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ project.row_index || '—' }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ project.type?.title || '—' }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ project.task_id || '—' }}</td>
                            <td class="py-4 pr-6 text-slate-600">{{ project.title || '—' }}</td>
                            <td v-for="column in columns" :key="column.key" class="py-4 pr-6 text-slate-600">
                                {{ project.values?.[column.key] ?? '—' }}
                            </td>
                            <td class="py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('project.export', { project: project.id, format: 'xlsx' })">
                                        XLSX
                                    </Link>
                                    <Link class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700" :href="route('project.export', { project: project.id, format: 'csv' })">
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

        <Pagination :meta="projects.meta" />
    </div>
</template>


<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'ProjectIndex',
    layout: AdminLayout,
    components: {
        Head,
        Link,
        Pagination,
    },
    props: {
        projects: Object,
        types: Array,
        tasks: Array,
        templates: Array,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            form: {
                type_id: this.filters?.type_id ?? '',
                task_id: this.filters?.task_id ?? '',
                template_id: this.filters?.template_id ?? '',
                q: this.filters?.q ?? '',
                columns: { ...(this.filters?.columns ?? {}) },
            },
        };
    },
    methods: {
        applyFilters() {
            this.$inertia.get(route('project.index'), {
                type_id: this.form.type_id || undefined,
                task_id: this.form.task_id || undefined,
                template_id: this.form.template_id || undefined,
                q: this.form.q || undefined,
                filters: this.form.columns,
            }, {
                preserveState: true,
                replace: true,
            });
        },
        resetFilters() {
            this.form = {
                type_id: '',
                task_id: '',
                template_id: '',
                q: '',
                columns: {},
            };
            this.applyFilters();
        },
    },
};
</script>
