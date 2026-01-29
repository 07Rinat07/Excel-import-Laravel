<template>
    <div class="space-y-8">
        <Head :title="`${$t('admin.templatesEditTitle')}: ${template.name}`" />

        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesEditTitle') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ template.name }}</h1>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $t('admin.templatesEditSubtitle') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Link class="w-full sm:w-auto rounded-full border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700" :href="route('admin.templates.index')">
                    {{ $t('actions.back') }}
                </Link>
                <button class="w-full sm:w-auto rounded-full bg-slate-900 px-4 py-2 text-center text-xs font-semibold text-white" type="button" @click="save">
                    {{ $t('actions.save') }}
                </button>
            </div>
        </header>

        <div v-if="$page.props.flash?.message" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-700">
            {{ $page.props.flash.message }}
        </div>
        <div v-if="$page.props.errors?.file" class="rounded-2xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm text-rose-700">
            {{ $page.props.errors.file }}
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesName') }}</label>
                    <input v-model="form.name" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm" type="text" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesType') }}</label>
                    <select v-model="form.type_id" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option :value="null">{{ $t('admin.templatesNoType') }}</option>
                        <option v-for="type in types" :key="type.id" :value="type.id">{{ type.title }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <input id="is_active" v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
                <label for="is_active" class="text-sm text-slate-700">{{ $t('admin.templatesStatusLabel') }}</label>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ $t('admin.templatesColumns') }}</h2>
                    <p class="text-sm text-slate-600">{{ $t('admin.templatesColumnsSubtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <input ref="columnsFile" type="file" class="hidden" accept=".xlsx,.csv,.tsv,.txt" @change="importColumns" />
                    <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="button" @click="selectColumnsFile">
                        {{ $t('admin.templatesImportColumns') }}
                    </button>
                    <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="button" @click="addColumn">
                        {{ $t('admin.templatesAddColumn') }}
                    </button>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                <div v-for="(column, index) in form.columns" :key="column.local_id" class="grid gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 lg:grid-cols-[1.4fr_1fr_0.8fr_1fr_0.6fr_0.4fr]">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesColumnLabel') }}</label>
                        <input v-model="column.label" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" type="text" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesColumnKey') }}</label>
                        <input v-model="column.key" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" type="text" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesColumnType') }}</label>
                        <select v-model="column.data_type" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            <option value="string">{{ $t('dataType.string') }}</option>
                            <option value="number">{{ $t('dataType.number') }}</option>
                            <option value="integer">{{ $t('dataType.integer') }}</option>
                            <option value="date">{{ $t('dataType.date') }}</option>
                            <option value="boolean">{{ $t('dataType.boolean') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.templatesColumnValidation') }}</label>
                        <input
                            v-model="column.validation_rules"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            type="text"
                            :placeholder="$t('admin.templatesColumnValidationHint')"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <input v-model="column.is_required" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
                        <span class="text-sm text-slate-700">{{ $t('admin.templatesColumnRequired') }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <input v-model.number="column.position" class="w-16 rounded-lg border border-slate-200 px-2 py-1 text-sm" type="number" min="0" />
                        <button class="text-xs font-semibold text-rose-600" type="button" @click="removeColumn(index, column)">
                            {{ $t('admin.templatesDeleteColumn') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="errors.columns" class="mt-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-600">
                {{ errors.columns }}
            </div>
        </section>
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'AdminTemplatesEdit',
    layout: AdminLayout,
    components: {
        Head,
        Link,
    },
    props: {
        template: Object,
        types: Array,
        errors: Object,
    },
    data() {
        return {
            deletedIds: [],
            form: {
                name: this.template.name,
                type_id: this.template.type_id,
                is_active: this.template.is_active,
                columns: this.template.columns.map((column, index) => ({
                    ...column,
                    validation_rules: Array.isArray(column.validation_rules) ? column.validation_rules.join('|') : (column.validation_rules ?? ''),
                    local_id: `${column.id}-${index}`,
                })),
            },
        };
    },
    methods: {
        selectColumnsFile() {
            this.$refs.columnsFile.click();
        },
        importColumns(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }
            const formData = new FormData();
            formData.append('file', file);
            this.$inertia.post(route('admin.templates.import_columns', this.template.id), formData, {
                forceFormData: true,
                onFinish: () => {
                    this.$refs.columnsFile.value = null;
                },
            });
        },
        addColumn() {
            const position = this.form.columns.length;
            this.form.columns.push({
                id: null,
                label: '',
                key: '',
                data_type: 'string',
                validation_rules: '',
                is_required: false,
                position,
                local_id: `new-${Date.now()}-${position}`,
            });
        },
        removeColumn(index, column) {
            if (column.id) {
                this.deletedIds.push(column.id);
            }
            this.form.columns.splice(index, 1);
        },
        save() {
            this.$inertia.put(route('admin.templates.update', this.template.id), {
                ...this.form,
                deleted_ids: this.deletedIds,
            });
        },
    },
};
</script>
