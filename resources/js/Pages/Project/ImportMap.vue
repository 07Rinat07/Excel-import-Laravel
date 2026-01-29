<template>
    <div class="space-y-8">
        <Head :title="$t('project.importMapTitle')" />

        <header>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.importCenter') }}</p>
            <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('project.importMapTitle') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $t('project.importMapSubtitleDynamic') }}
            </p>
            <p v-if="task.file" class="mt-2 text-xs text-slate-500">
                {{ task.file.title }}
            </p>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div v-if="$page.props.errors?.mapping" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700">
                {{ $page.props.errors.mapping }}
            </div>

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500">
                    {{ selectedCount }} {{ $t('labels.selected') }}
                </div>
                <div v-if="available_sheets.length" class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="uppercase tracking-[0.2em] text-slate-400">{{ $t('project.importMapSheetSelect') }}</span>
                    <select
                        v-model.number="selectedSheetIndex"
                        class="rounded-full border border-slate-300 px-3 py-1 text-xs"
                        @change="changeSheet"
                    >
                        <option v-for="sheet in available_sheets" :key="`sheet-${sheet.index}`" :value="sheet.index">
                            {{ sheet.name }}
                        </option>
                    </select>
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
                                <tr v-if="!hasPreviewRows">
                                    <td :colspan="columns.length" class="border-t border-slate-100 py-3 text-[0.7rem] text-slate-500">
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
                    class="rounded-full bg-slate-900 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white"
                    :disabled="selectedCount === 0"
                    @click="submitMapping"
                >
                    {{ $t('project.importMapStart') }}
                </button>
            </div>
        </section>
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'ImportMap',
    layout: AdminLayout,
    components: {
        Head,
    },
    props: {
        task: {
            type: Object,
            default: () => ({}),
        },
        type: {
            type: Object,
            default: () => null,
        },
        headers: {
            type: Array,
            default: () => [],
        },
        preview_rows: {
            type: Array,
            default: () => [],
        },
        available_sheets: {
            type: Array,
            default: () => [],
        },
        selected_sheet_index: Number,
        data_types: Array,
        mapping_suggestion: Object,
        template_columns: Object,
    },
    data() {
        return {
            columns: [],
            selectedSheetIndex: this.selected_sheet_index ?? 0,
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
        hasPreviewRows() {
            return this.preview_rows.length > 0;
        },
        sheetNames() {
            return this.available_sheets.map((sheet) => sheet.name);
        },
    },
    mounted() {
        const suggestions = this.mapping_suggestion?.mapping || {};
        const templateColumns = this.template_columns || {};

        this.columns = this.headers.map((header) => {
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
    methods: {
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
        submitMapping() {
            this.$inertia.post(route('project.import.map.store', this.task.id), {
                columns: this.columns,
                sheet_index: this.selectedSheetIndex,
            });
        },
        changeSheet() {
            this.$inertia.get(
                route('project.import.map', this.task.id),
                { sheet_index: this.selectedSheetIndex },
                { preserveState: true, preserveScroll: true, replace: true }
            );
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
