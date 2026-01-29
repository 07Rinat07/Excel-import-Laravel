<template>
    <div class="space-y-8">
        <Head :title="$t('labels.importExcel')" />

        <header>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.importCenter') }}</p>
            <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('project.importTitle') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $t('project.importSubtitle') }}
            </p>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="flex flex-wrap items-center gap-4 text-xs uppercase tracking-[0.2em] text-slate-400">
                <span :class="stepClass('upload')">{{ $t('actions.startImport') }}</span>
                <span class="text-slate-300">—</span>
                <span :class="stepClass('map')">{{ $t('project.importMapTitle') }}</span>
                <span class="text-slate-300">—</span>
                <span :class="stepClass('done')">{{ $t('labels.success') }}</span>
            </div>
        </section>

        <section v-if="step === 'upload'" class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <form class="grid gap-6 lg:grid-cols-[1fr_auto]" @submit.prevent="prepareImport">
                <div class="space-y-4">
                    <div v-if="errorMessage" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ errorMessage }}
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('project.importTypeLabel') }}</label>
                        <select v-model="typeId" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                            <option disabled value="">{{ $t('project.importTypePlaceholder') }}</option>
                            <option v-for="type in types" :key="type.id" :value="type.id">
                                {{ type.title }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('project.importFileLabel') }}</label>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <input @change="setExcel" type="file" ref="file" class="hidden" accept=".xlsx,.xls,.xlsm,.csv,.tsv" />
                            <button type="button" class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" @click="selectExcel">
                                {{ $t('actions.chooseFile') }}
                            </button>
                            <span v-if="file" class="text-sm text-slate-600">{{ file.name }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-end">
                    <button
                        class="w-full sm:w-auto rounded-full bg-slate-900 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white disabled:opacity-50 disabled:cursor-not-allowed"
                        type="submit"
                        :disabled="!file || !typeId || loading"
                    >
                        {{ loading ? $t('labels.loading') : $t('actions.startImport') }}
                    </button>
                </div>
            </form>
        </section>

        <section v-else-if="step === 'map'" class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div v-if="errorMessage" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700">
                {{ errorMessage }}
            </div>

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500">
                    {{ selectedCount }} {{ $t('labels.selected') }}
                </div>
                <div v-if="available_sheets.length" class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="uppercase tracking-[0.2em] text-slate-400">{{ $t('project.importMapSheetSelect') }}</span>
                    <span class="rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-600">
                        {{ available_sheets[selectedSheetIndex]?.name || '-' }}
                    </span>
                </div>
                <button
                    type="button"
                    class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                    @click="toggleAll"
                >
                    {{ allSelected ? $t('actions.clearSelection') : $t('actions.selectAll') }}
                </button>
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-slate-50/60 p-2">
                    <table class="min-w-[720px] text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-3 pr-4">{{ $t('labels.active') }}</th>
                                <th class="pb-3 pr-6">{{ $t('project.importMapExcelColumn') }}</th>
                                <th class="pb-3 pr-6">{{ $t('project.importMapColumnName') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.dataType') }}</th>
                                <th class="pb-3">{{ $t('project.importMapRequired') }}</th>
                                <th class="pb-3 pr-6">{{ $t('project.importMapValidationRules') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="column in columns"
                                :key="column.index"
                                class="border-t border-slate-200 bg-white"
                                :class="column.required ? 'bg-rose-50/40' : ''"
                            >
                                <td class="py-3 pr-4">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-slate-900"
                                        v-model="column.include"
                                    />
                                </td>
                                <td class="py-3 pr-6 text-slate-700">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-900">{{ column.label }}</span>
                                        <span
                                            v-if="column.isUnnamed"
                                            class="rounded-full border border-slate-300 px-2 py-0.5 text-[0.6rem] font-semibold uppercase tracking-[0.2em] text-slate-500"
                                        >
                                            {{ $t('project.importMapUnnamedHint') }}
                                        </span>
                                        <span
                                            v-if="column.suggested"
                                            class="rounded-full border border-emerald-300 bg-emerald-50 px-2 py-0.5 text-[0.55rem] font-semibold uppercase tracking-[0.2em] text-emerald-700"
                                        >
                                            Smart
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 pr-6">
                                    <input
                                        v-model="column.name"
                                        type="text"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                                        :disabled="!column.include"
                                        :placeholder="$t('project.importMapColumnNamePlaceholder')"
                                    />
                                    <p v-if="column.include" class="mt-2 text-[0.7rem] uppercase tracking-[0.2em] text-slate-400">
                                        {{ $t('project.importMapTypeHint', { type: typeLabel(column.data_type) }) }}
                                    </p>
                                    <p v-if="column.include" class="text-xs text-slate-500">
                                        {{ $t('project.importMapValidationHint', { rule: validationLabel(column) }) }}
                                    </p>
                                </td>
                                <td class="py-3 pr-6">
                                    <select
                                        v-model="column.data_type"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                                        :disabled="!column.include"
                                    >
                                        <option v-for="type in dataTypes" :key="type" :value="type">
                                            {{ $t(`dataType.${type}`) }}
                                        </option>
                                    </select>
                                </td>
                                <td class="py-3">
                                    <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                                        <input
                                            v-model="column.required"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-slate-300 text-rose-500"
                                            :disabled="!column.include"
                                        />
                                        <span>{{ $t('project.importMapRequired') }}</span>
                                    </label>
                                </td>
                                <td class="py-3 pr-6">
                                    <input
                                        v-model="column.validation_rules"
                                        type="text"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                                        :disabled="!column.include"
                                        :placeholder="$t('project.importMapValidationPlaceholder')"
                                    />
                                    <p v-if="column.include" class="mt-1 text-[0.65rem] text-slate-400">
                                        {{ $t('project.importMapValidationHintInput') }}
                                    </p>
                                </td>
                            </tr>
                            <tr v-if="columns.length === 0">
                                <td colspan="6" class="py-6 text-center text-xs text-slate-500">
                                    {{ $t('project.importMapPreviewEmpty') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col rounded-2xl border border-slate-200/80 bg-white p-4 shadow-inner shadow-slate-100">
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">{{ $t('project.importMapPreviewTitle') }}</p>
                        <h3 class="mt-1 text-sm font-semibold text-slate-900">{{ $t('project.importMapPreviewDescription') }}</h3>
                    </div>
                    <div class="mt-4 flex flex-col gap-3 overflow-hidden">
                        <div v-if="sheetNames.length" class="flex flex-wrap items-center gap-2 text-[0.6rem] uppercase tracking-[0.3em] text-slate-400">
                            <span>{{ $t('project.importMapSheetListTitle') }}:</span>
                            <span
                                v-for="(name, index) in sheetNames"
                                :key="`sheet-${index}`"
                                class="rounded-full border px-3 py-1 text-[0.55rem] font-semibold tracking-[0.2em]"
                                :class="index === selectedSheetIndex ? 'border-slate-900 bg-slate-900/5 text-slate-900' : 'border-slate-200 text-slate-500'"
                            >
                                {{ name }}
                            </span>
                        </div>
                        <div v-else class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">
                            {{ $t('project.importMapSheetListEmpty') }}
                        </div>
                    </div>
                    <div class="mt-2 flex-1 overflow-x-auto">
                        <table class="w-full table-fixed text-[0.6rem] sm:text-[0.7rem]">
                            <thead>
                                <tr class="text-left uppercase tracking-[0.3em] text-slate-400">
                                    <th
                                        v-for="column in columns"
                                        :key="`preview-${column.index}`"
                                        class="pb-2 pr-2 text-[0.55rem]"
                                    >
                                        <div class="flex items-center gap-1">
                                            <span class="font-semibold text-slate-600">{{ column.label }}</span>
                                            <span
                                                v-if="column.isUnnamed"
                                                class="rounded-full border border-slate-200 px-2 py-0.5 text-[0.45rem] font-semibold tracking-[0.2em] text-slate-500"
                                            >
                                                {{ $t('project.importMapUnnamedHint') }}
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, rowIndex) in preview_rows" :key="`preview-row-${rowIndex}`" class="text-[0.65rem]">
                                    <td
                                        v-for="(_, colIndex) in columns"
                                        :key="`preview-${rowIndex}-${colIndex}`"
                                        class="border-t border-slate-100 py-1 pr-2 text-slate-700"
                                    >
                                        {{ getPreviewCell(row, colIndex) }}
                                    </td>
                                </tr>
                                <tr v-if="preview_rows.length === 0">
                                    <td :colspan="columns.length || 1" class="border-t border-slate-100 py-3 text-[0.7rem] text-slate-500">
                                        {{ $t('project.importMapPreviewEmpty') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500">
                    {{ selectedCount }} {{ $t('labels.selected') }}
                </div>
                <button
                    type="button"
                    class="rounded-full bg-slate-900 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="selectedCount === 0 || loading"
                    @click="submitMapping"
                >
                    {{ loading ? $t('labels.loading') : $t('project.importMapStart') }}
                </button>
            </div>
        </section>

        <section v-else class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-6 shadow-lg shadow-emerald-200/40">
            <h2 class="text-lg font-semibold text-emerald-800">{{ $t('project.importStatus.processing') }}</h2>
            <p class="mt-2 text-sm text-emerald-700">
                {{ $t('home.queueFirstBody') }}
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <Link class="rounded-full border border-emerald-300 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700" :href="route('task.index')">
                    {{ $t('task.title') }}
                </Link>
                <Link class="rounded-full border border-emerald-300 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700" :href="route('project.import')">
                    {{ $t('actions.newImport') }}
                </Link>
            </div>
        </section>
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'ImportWizard',
    layout: AdminLayout,
    components: {
        Head,
        Link,
    },
    props: {
        types: Array,
        recentTasks: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            step: 'upload',
            file: null,
            typeId: '',
            loading: false,
            errorMessage: '',
            task: null,
            headers: [],
            preview_rows: [],
            available_sheets: [],
            selectedSheetIndex: 0,
            data_types: [],
            mapping_suggestion: null,
            template_columns: {},
            columns: [],
        };
    },
    computed: {
        selectedCount() {
            return this.columns.filter((column) => column.include).length;
        },
        allSelected() {
            return this.columns.length > 0 && this.columns.every((column) => column.include);
        },
        dataTypes() {
            return this.data_types || ['string', 'number', 'integer', 'date', 'boolean'];
        },
        sheetNames() {
            return this.available_sheets.map((sheet) => sheet.name);
        },
    },
    methods: {
        stepClass(name) {
            return this.step === name ? 'text-slate-900 font-semibold' : 'text-slate-400';
        },
        selectExcel() {
            this.$refs.file.click();
        },
        setExcel(event) {
            this.file = event.target.files[0];
            this.errorMessage = '';
        },
        async prepareImport() {
            if (!this.file || !this.typeId) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const formData = new FormData();
                formData.append('file', this.file);
                formData.append('type_id', this.typeId);

                const response = await axios.post(route('project.import.prepare'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                    },
                });

                const payload = response.data;
                this.task = payload.task;
                this.headers = payload.headers || [];
                this.preview_rows = payload.preview_rows || [];
                this.available_sheets = payload.available_sheets || [];
                this.selectedSheetIndex = payload.selected_sheet_index ?? 0;
                this.data_types = payload.data_types || [];
                this.mapping_suggestion = payload.mapping_suggestion || null;
                this.template_columns = payload.template_columns || {};
                this.buildColumns();
                this.step = 'map';
            } catch (error) {
                const errors = error.response?.data?.errors;
                if (errors && typeof errors === 'object') {
                    const messages = Object.values(errors).flat().filter(Boolean);
                    this.errorMessage = messages.length
                        ? messages.join(' ')
                        : (error.response?.data?.message || this.$t('project.importErrorGeneral'));
                } else {
                    this.errorMessage = error.response?.data?.message ||
                                       error.message ||
                                       this.$t('project.importErrorGeneral');
                }
            } finally {
                this.loading = false;
            }
        },
        buildColumns() {
            const suggestions = this.mapping_suggestion?.mapping || {};
            const templateColumns = this.template_columns || {};

            this.columns = (this.headers || []).map((header) => {
                const suggestedId = suggestions[header.index];
                const templateColumn = suggestedId ? templateColumns[suggestedId] : null;
                const validationRules = templateColumn?.validation_rules || [];

                return {
                    index: header.index,
                    label: header.label,
                    name: templateColumn?.label ?? header.label,
                    include: true,
                    data_type: templateColumn?.data_type ?? 'string',
                    required: templateColumn?.is_required ?? false,
                    validation_rules: Array.isArray(validationRules) ? validationRules.join('|') : '',
                    isUnnamed: header.is_unnamed ?? false,
                    suggested: !!templateColumn,
                };
            });
        },
        toggleAll() {
            const next = !this.allSelected;
            this.columns = this.columns.map((column) => ({
                ...column,
                include: next,
            }));
        },
        typeLabel(type) {
            return this.$t(`dataType.${type}`) || type;
        },
        validationLabel(column) {
            const typeLabel = this.typeLabel(column.data_type);
            if (column.required) {
                return `${typeLabel}, ${this.$t('project.importMapRequired')}`;
            }
            return typeLabel;
        },
        async submitMapping() {
            if (!this.task) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                await axios.post(
                    route('project.import.map.json', this.task.id),
                    {
                        columns: this.columns,
                        sheet_index: this.selectedSheetIndex,
                    },
                    { headers: { 'Accept': 'application/json' } }
                );
                this.step = 'done';
            } catch (error) {
                const errors = error.response?.data?.errors;
                if (errors && typeof errors === 'object') {
                    const messages = Object.values(errors).flat().filter(Boolean);
                    this.errorMessage = messages.length
                        ? messages.join(' ')
                        : (error.response?.data?.message || this.$t('project.importErrorGeneral'));
                } else {
                    this.errorMessage = error.response?.data?.message ||
                                       error.message ||
                                       this.$t('project.importErrorGeneral');
                }
            } finally {
                this.loading = false;
            }
        },
        getPreviewCell(row, columnIndex) {
            if (!Array.isArray(row)) {
                return this.$t('project.importMapEmptyCell');
            }

            const value = row[columnIndex];
            if (value === null || value === '') {
                return this.$t('project.importMapEmptyCell');
            }

            return value;
        },
    },
};
</script>
