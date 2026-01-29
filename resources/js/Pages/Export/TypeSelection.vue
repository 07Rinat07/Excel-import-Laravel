<template>
    <div class="space-y-6">
        <Head :title="$t('labels.exportData')" />

        <header>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.exportCenter') }}</p>
            <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('labels.selectColumnsToExport') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $t('labels.selectColumnsExportDescription') }}
            </p>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <form @submit.prevent="submitExport" class="space-y-6">
                <!-- Error Messages -->
                <div v-if="$page.props.errors?.error" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $page.props.errors.error }}
                </div>

                <!-- Column Selection -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900">{{ $t('labels.columns') }}</h3>
                        <button
                            type="button"
                            class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 hover:text-slate-900"
                            @click="toggleAll"
                        >
                            {{ allSelected ? $t('actions.clearSelection') : $t('actions.selectAll') }}
                        </button>
                    </div>

                    <div class="space-y-2 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                        <label v-for="column in columns" :key="column.id" class="flex items-center gap-3 py-2">
                            <input
                                type="checkbox"
                                v-model="selectedColumns"
                                :value="column.id"
                                class="h-4 w-4 rounded border-slate-300"
                            />
                            <span class="text-sm text-slate-900">{{ column.label }}</span>
                            <span class="text-xs text-slate-500">({{ column.key }})</span>
                        </label>
                    </div>

                    <p class="text-xs text-slate-500">
                        {{ selectedColumns.length }} / {{ columns.length }} {{ $t('labels.columnsSelected') }}
                    </p>
                </div>

                <!-- Format Selection -->
                <div class="space-y-3">
                    <label class="text-sm font-semibold text-slate-900">{{ $t('labels.exportFormat') }}</label>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:bg-slate-50">
                            <input
                                type="radio"
                                v-model="format"
                                value="xlsx"
                                class="h-4 w-4 border-slate-300"
                            />
                            <span class="text-sm text-slate-900">Excel (.xlsx)</span>
                        </label>
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:bg-slate-50">
                            <input
                                type="radio"
                                v-model="format"
                                value="csv"
                                class="h-4 w-4 border-slate-300"
                            />
                            <span class="text-sm text-slate-900">CSV</span>
                        </label>
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:bg-slate-50">
                            <input
                                type="radio"
                                v-model="format"
                                value="tsv"
                                class="h-4 w-4 border-slate-300"
                            />
                            <span class="text-sm text-slate-900">TSV</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-6">
                    <Link
                        href="/"
                        class="flex-1 rounded-full border border-slate-300 px-6 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        {{ $t('actions.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="selectedColumns.length === 0 || loading"
                        class="flex-1 rounded-full bg-slate-900 px-6 py-2 text-sm font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? $t('labels.exporting') : $t('labels.exportData') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'TypeExportSelection',
    layout: AdminLayout,
    components: {
        Head,
        Link,
    },
    props: {
        type: Object,
        columns: Array,
    },
    data() {
        return {
            selectedColumns: [],
            format: 'xlsx',
            loading: false,
        };
    },
    computed: {
        allSelected() {
            return this.selectedColumns.length === this.columns.length;
        },
    },
    mounted() {
        // By default, select all columns
        this.selectedColumns = this.columns.map(col => col.id);
    },
    methods: {
        toggleAll() {
            if (this.allSelected) {
                this.selectedColumns = [];
            } else {
                this.selectedColumns = this.columns.map(col => col.id);
            }
        },
        async submitExport() {
            if (this.selectedColumns.length === 0) {
                return;
            }

            this.loading = true;

            try {
                const response = await axios.post(
                    route('type.export.select.store', this.type.id),
                    {
                        columns: this.selectedColumns,
                        format: this.format,
                    },
                    {
                        responseType: 'blob',
                    }
                );

                // Trigger download
                const url = window.URL.createObjectURL(response.data);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `type-${this.type.id}.${this.format}`);
                document.body.appendChild(link);
                link.click();
                link.parentNode.removeChild(link);
            } catch (error) {
                console.error('Export error:', error);
                alert(this.$t('labels.exportError'));
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
