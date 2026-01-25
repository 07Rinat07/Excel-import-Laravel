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
                <button
                    type="button"
                    class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                    @click="toggleAll"
                >
                    {{ allSelected ? $t('actions.clearSelection') : $t('actions.selectAll') }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[720px] text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3 pr-4">{{ $t('labels.active') }}</th>
                            <th class="pb-3 pr-6">{{ $t('project.importMapExcelColumn') }}</th>
                            <th class="pb-3 pr-6">{{ $t('project.importMapColumnName') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.dataType') }}</th>
                            <th class="pb-3">{{ $t('project.importMapRequired') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="column in columns"
                            :key="column.index"
                            class="border-t border-slate-200"
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
                                {{ column.label }}
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
                        </tr>
                    </tbody>
                </table>
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
        task: Object,
        type: Object,
        headers: Array,
        data_types: Array,
    },
    data() {
        return {
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
    },
    mounted() {
        this.columns = this.headers.map((header) => ({
            index: header.index,
            label: header.label,
            name: header.label,
            include: true,
            data_type: 'string',
            required: false,
        }));
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
            });
        },
    },
};
</script>
