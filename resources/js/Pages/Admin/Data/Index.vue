<template>
    <div class="space-y-8">
        <Head :title="$t('admin.dataTitle')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('admin.dataTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('admin.dataSubtitle') }}</p>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40 space-y-4">
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-[240px]">
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.dataTableLabel') }}</label>
                    <select v-model="selectedTable" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option v-for="tableName in tables" :key="tableName" :value="tableName">
                            {{ tableName }}
                        </option>
                    </select>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                        @click="reloadTable"
                    >
                        {{ $t('actions.refresh') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                        @click="openCreate = !openCreate"
                    >
                        {{ $t('admin.dataCreateRow') }}
                    </button>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.dataFilters') }}</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                        <select v-model="filterColumn" class="rounded-xl border border-slate-200 px-3 py-2 text-xs">
                            <option value="">{{ $t('admin.dataFilterColumn') }}</option>
                            <option v-for="column in columns" :key="column.name" :value="column.name">
                                {{ column.name }}
                            </option>
                        </select>
                        <input
                            v-model="filterValue"
                            type="text"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-xs"
                            :placeholder="$t('admin.dataFilterValue')"
                        />
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white" type="button" @click="applyFilter">
                            {{ $t('actions.applyFilters') }}
                        </button>
                        <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="button" @click="clearFilters">
                            {{ $t('actions.resetFilters') }}
                        </button>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.dataExport') }}</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                        <select v-model="exportFormat" class="rounded-xl border border-slate-200 px-3 py-2 text-xs">
                            <option value="xlsx">XLSX</option>
                            <option value="csv">CSV</option>
                            <option value="tsv">TSV</option>
                        </select>
                        <select v-model="exportMode" class="rounded-xl border border-slate-200 px-3 py-2 text-xs">
                            <option value="selected">{{ $t('admin.dataExportSelected') }}</option>
                            <option value="filtered">{{ $t('admin.dataExportFiltered') }}</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">
                            {{ $t('admin.dataExportColumns') }}
                        </p>
                        <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                            <label class="mb-2 inline-flex items-center gap-2 text-xs text-slate-600">
                                <input type="checkbox" :checked="allColumnsSelected" @change="toggleAllColumns" />
                                {{ $t('admin.dataExportColumnsAll') }}
                            </label>
                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                <label v-for="column in columns" :key="column.name" class="inline-flex items-center gap-2 text-xs text-slate-700">
                                    <input v-model="exportColumns" type="checkbox" :value="column.name" />
                                    <span>{{ column.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white" type="button" @click="exportData">
                            {{ $t('actions.export') }}
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.dataMaintenance') }}</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                        <input v-model="includeFiles" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
                        {{ $t('admin.dataIncludeFiles') }}
                    </label>
                    <button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white" type="button" @click="backupDatabase">
                        {{ $t('admin.dataBackup') }}
                    </button>
                    <button class="rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600" type="button" @click="cleanupAll">
                        {{ $t('admin.dataCleanupAll') }}
                    </button>
                    <button class="rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600" type="button" @click="cleanupSelected">
                        {{ $t('admin.dataCleanupSelected') }}
                    </button>
                </div>
                <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">
                        {{ $t('admin.dataCleanupTables') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">{{ $t('admin.dataCleanupTablesHint') }}</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        <label v-for="tableName in tables" :key="tableName" class="inline-flex items-center gap-2 text-xs text-slate-700">
                            <input v-model="selectedTables" type="checkbox" :value="tableName" />
                            <span>{{ tableName }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40 space-y-4">
            <div v-if="openCreate" class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.dataCreateRow') }}</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="column in columns" :key="column.name">
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ column.name }}</label>
                        <input
                            v-model="createRow[column.name]"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                        />
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white" type="button" @click="createData">
                        {{ $t('actions.save') }}
                    </button>
                    <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="button" @click="openCreate = false">
                        {{ $t('actions.cancel') }}
                    </button>
                </div>
            </div>

            <div v-if="rows">
                <div class="overflow-x-auto">
                    <table class="min-w-[960px] text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-3 pr-4">
                                    <input type="checkbox" :checked="allSelected" @change="toggleAll" />
                                </th>
                                <th v-for="column in columns" :key="column.name" class="pb-3 pr-6">
                                    {{ column.name }}
                                </th>
                                <th class="pb-3 text-right">{{ $t('labels.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in rows.data" :key="rowKey(row)" class="border-t border-slate-200">
                                <td class="py-3 pr-4">
                                    <input type="checkbox" :value="rowKey(row)" v-model="selectedRows" :disabled="!primaryKey" />
                                </td>
                                <td v-for="column in columns" :key="column.name" class="py-3 pr-6">
                                    <input
                                        v-if="editingRowId === rowKey(row)"
                                        v-model="editRow[column.name]"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs"
                                    />
                                    <span v-else class="text-slate-700">
                                        {{ row[column.name] ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button
                                            v-if="editingRowId !== rowKey(row)"
                                            class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700"
                                            type="button"
                                            @click="startEdit(row)"
                                        >
                                            {{ $t('actions.edit') }}
                                        </button>
                                        <button
                                            v-else
                                            class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white"
                                            type="button"
                                            @click="saveEdit"
                                        >
                                            {{ $t('actions.save') }}
                                        </button>
                                        <button
                                            class="rounded-full border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600"
                                            type="button"
                                            :disabled="!primaryKey"
                                            @click="deleteRow(row)"
                                        >
                                            {{ $t('actions.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination v-if="rows" :meta="rows.meta" />
            </div>
        </section>
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'AdminDataIndex',
    layout: AdminLayout,
    components: {
        Head,
        Pagination,
    },
    props: {
        tables: Array,
        table: String,
        columns: Array,
        primaryKey: String,
        rows: Object,
        filters: Object,
    },
    data() {
        return {
            selectedTable: this.table || '',
            filterColumn: '',
            filterValue: '',
            selectedRows: [],
            exportFormat: 'xlsx',
            exportMode: 'selected',
            exportColumns: this.columns.map((column) => column.name),
            includeFiles: false,
            selectedTables: [],
            openCreate: false,
            createRow: {},
            editingRowId: null,
            editRow: {},
        };
    },
    computed: {
        allSelected() {
            if (!this.primaryKey || !this.rows) {
                return false;
            }
            return this.rows.data.length > 0 && this.rows.data.every((row) => this.selectedRows.includes(this.rowKey(row)));
        },
        allColumnsSelected() {
            if (!this.columns.length) {
                return false;
            }
            return this.columns.every((column) => this.exportColumns.includes(column.name));
        },
    },
    mounted() {
        this.resetCreateRow();
        if (this.selectedTable) {
            this.selectedTables = [this.selectedTable];
        }
        if (this.filters && typeof this.filters === 'object') {
            const entries = Object.entries(this.filters);
            if (entries.length > 0) {
                this.filterColumn = entries[0][0];
                this.filterValue = entries[0][1];
            }
        }
    },
    methods: {
        rowKey(row) {
            if (!this.primaryKey) {
                return JSON.stringify(row);
            }
            return row[this.primaryKey];
        },
        resetCreateRow() {
            const next = {};
            this.columns.forEach((column) => {
                next[column.name] = '';
            });
            this.createRow = next;
        },
        syncExportColumns() {
            this.exportColumns = this.columns.map((column) => column.name);
        },
        reloadTable() {
            this.$inertia.get(route('admin.data.index'), { table: this.selectedTable });
        },
        applyFilter() {
            const filters = {};
            if (this.filterColumn && this.filterValue !== '') {
                filters[this.filterColumn] = this.filterValue;
            }
            this.$inertia.get(route('admin.data.index'), { table: this.selectedTable, filters });
        },
        clearFilters() {
            this.filterColumn = '';
            this.filterValue = '';
            this.$inertia.get(route('admin.data.index'), { table: this.selectedTable });
        },
        toggleAll(event) {
            if (!this.primaryKey) {
                return;
            }
            if (event.target.checked) {
                this.selectedRows = this.rows.data.map((row) => this.rowKey(row));
            } else {
                this.selectedRows = [];
            }
        },
        toggleAllColumns(event) {
            if (event.target.checked) {
                this.syncExportColumns();
            } else {
                this.exportColumns = [];
            }
        },
        startEdit(row) {
            if (!this.primaryKey) {
                return;
            }
            this.editingRowId = this.rowKey(row);
            this.editRow = { ...row };
        },
        saveEdit() {
            this.$inertia.patch(route('admin.data.update'), {
                table: this.selectedTable,
                id: this.editingRowId,
                data: this.editRow,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.editingRowId = null;
                    this.editRow = {};
                },
            });
        },
        deleteRow(row) {
            if (!this.primaryKey) {
                return;
            }
            if (!confirm(this.$t('admin.dataDeleteConfirm'))) {
                return;
            }
            this.$inertia.delete(route('admin.data.destroy'), {
                data: {
                    table: this.selectedTable,
                    id: this.rowKey(row),
                },
            });
        },
        createData() {
            this.$inertia.post(route('admin.data.store'), {
                table: this.selectedTable,
                data: this.createRow,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.openCreate = false;
                    this.resetCreateRow();
                },
            });
        },
        exportData() {
            const ids = this.exportMode === 'selected' ? this.selectedRows : [];
            if (!this.exportColumns.length) {
                alert(this.$t('admin.dataExportNoColumns'));
                return;
            }
            if (this.exportMode === 'selected' && ids.length === 0) {
                alert(this.$t('admin.dataExportNoRows'));
                return;
            }

            const url = new URL(route('admin.data.export'), window.location.origin);
            url.searchParams.set('table', this.selectedTable);
            url.searchParams.set('format', this.exportFormat);
            this.exportColumns.forEach((column) => {
                url.searchParams.append('columns[]', column);
            });
            if (this.exportMode === 'selected') {
                ids.forEach((id) => {
                    url.searchParams.append('ids[]', id);
                });
            } else if (this.filters) {
                Object.entries(this.filters).forEach(([key, value]) => {
                    if (value !== '') {
                        url.searchParams.append(`filters[${key}]`, value);
                    }
                });
            }

            window.location.assign(url.toString());
        },
        backupDatabase() {
            const url = new URL(route('admin.data.backup'), window.location.origin);
            if (this.includeFiles) {
                url.searchParams.set('include_files', '1');
            }
            window.location.assign(url.toString());
        },
        cleanupAll() {
            if (!confirm(this.$t('admin.dataCleanupAllConfirm'))) {
                return;
            }
            this.$inertia.post(route('admin.data.cleanup'), {
                mode: 'all',
            });
        },
        cleanupSelected() {
            if (!confirm(this.$t('admin.dataCleanupSelectedConfirm'))) {
                return;
            }
            if (this.selectedTables.length === 0) {
                alert(this.$t('admin.dataCleanupSelectTables'));
                return;
            }
            this.$inertia.post(route('admin.data.cleanup'), {
                mode: 'selected',
                tables: this.selectedTables,
            });
        },
    },
    watch: {
        columns() {
            this.resetCreateRow();
            this.syncExportColumns();
            this.selectedRows = [];
        },
        selectedTable() {
            if (this.selectedTable && !this.selectedTables.includes(this.selectedTable)) {
                this.selectedTables = [...this.selectedTables, this.selectedTable];
            }
            this.reloadTable();
        },
    },
};
</script>
