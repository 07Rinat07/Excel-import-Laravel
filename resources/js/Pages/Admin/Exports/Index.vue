<template>
    <div class="space-y-8">
        <Head :title="$t('admin.exportsTitle')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('admin.exportsTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('admin.exportsSubtitle') }}</p>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.export') }}</p>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $t('admin.exportsBuilderTitle') }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $t('admin.exportsBuilderSubtitle') }}</p>
                </div>
                <button
                    type="button"
                    class="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
                    @click="toggleAllColumns"
                    :disabled="currentColumns.length === 0"
                >
                    {{ allSelected ? $t('actions.clearSelection') : $t('actions.selectAll') }}
                </button>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-[1.2fr_1fr_1fr]">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.exportsPresetSelect') }}</label>
                    <div class="mt-2 flex gap-2">
                        <select v-model="selectedPresetId" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm" @change="applyPreset">
                            <option value="">{{ $t('admin.exportsPresetPlaceholder') }}</option>
                            <option v-for="preset in presets" :key="preset.id" :value="preset.id">
                                {{ preset.name }}
                            </option>
                        </select>
                        <button
                            v-if="selectedPresetId"
                            type="button"
                            class="rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600"
                            @click="deletePreset"
                        >
                            {{ $t('actions.delete') }}
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.exportsPresetName') }}</label>
                    <div class="mt-2 flex gap-2">
                        <input
                            v-model="presetName"
                            type="text"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm"
                            :placeholder="$t('admin.exportsPresetNamePlaceholder')"
                        />
                        <button
                            type="button"
                            class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white"
                            :disabled="presetName.trim() === '' || !canExport"
                            @click="savePreset"
                        >
                            {{ $t('actions.save') }}
                        </button>
                    </div>
                </div>
                <div class="flex items-end">
                    <button
                        type="button"
                        class="w-full rounded-full border border-slate-300 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700"
                        :disabled="!canExport"
                        @click="queueExport"
                    >
                        {{ $t('admin.exportsQueue') }}
                    </button>
                </div>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-[1.2fr_1fr_1fr_1fr_0.6fr]">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.exportsSourceLabel') }}</label>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        <select v-model="sourceType" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                            <option value="type">{{ $t('admin.exportsSourceType') }}</option>
                            <option value="task">{{ $t('admin.exportsSourceTask') }}</option>
                        </select>
                        <select v-model="sourceId" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                            <option disabled value="">{{ $t('admin.exportsSourcePlaceholder') }}</option>
                            <option
                                v-for="item in sourceOptions"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.title }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.exportsUserLabel') }}</label>
                    <div class="mt-2">
                        <select v-model="userId" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm" :disabled="sourceType !== 'type'">
                            <option value="">{{ $t('admin.exportsUserPlaceholder') }}</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }}{{ user.email ? ` (${user.email})` : '' }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.exportsFormatLabel') }}</label>
                    <div class="mt-2">
                        <select v-model="format" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                            <option value="xlsx">XLSX</option>
                            <option value="csv">CSV</option>
                            <option value="tsv">TSV</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $t('admin.exportsSheetLabel') }}</label>
                    <div class="mt-2">
                        <input
                            v-model="sheetName"
                            type="text"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm"
                            :placeholder="$t('admin.exportsSheetPlaceholder')"
                        />
                    </div>
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        class="w-full rounded-full bg-slate-900 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white"
                        :disabled="!canExport"
                        @click="startExport"
                    >
                        {{ $t('actions.export') }}
                    </button>
                </div>
            </div>

            <div v-if="sourceId && currentColumns.length === 0" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                {{ $t('admin.exportsNoColumns') }}
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-[640px] text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3 pr-4">
                                {{ $t('labels.active') }}
                            </th>
                            <th class="pb-3 pr-6">{{ $t('labels.label') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.key') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.type') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.required') }}</th>
                            <th class="pb-3">{{ $t('admin.exportsCustomHeader') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="column in currentColumns" :key="column.id" class="border-t border-slate-200">
                            <td class="py-3 pr-4">
                                <input
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-slate-900"
                                    :checked="selectedColumns[column.id] === true"
                                    @change="toggleColumn(column.id, $event.target.checked)"
                                />
                            </td>
                            <td class="py-3 pr-6 text-slate-700">{{ column.label }}</td>
                            <td class="py-3 pr-6 text-slate-500">{{ column.key }}</td>
                            <td class="py-3 pr-6 text-slate-500">{{ column.data_type }}</td>
                            <td class="py-3 pr-6 text-slate-500">
                                <span v-if="column.is_required" class="rounded-full border border-rose-200 px-2 py-0.5 text-[0.65rem] font-semibold text-rose-600">
                                    {{ $t('labels.required') }}
                                </span>
                                <span v-else class="text-slate-400">—</span>
                            </td>
                            <td class="py-3">
                                <input
                                    v-model="customLabels[column.id]"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs"
                                    :placeholder="$t('admin.exportsCustomHeaderPlaceholder')"
                                    :disabled="selectedColumns[column.id] !== true"
                                />
                            </td>
                        </tr>
                        <tr v-if="currentColumns.length === 0">
                            <td colspan="6" class="py-6 text-center text-sm text-slate-500">
                                {{ $t('admin.exportsSelectSource') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div v-if="exports.data.length === 0" class="text-center text-sm text-slate-600">
                {{ $t('admin.exportsEmpty') }}
            </div>
            <div v-else class="space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="item in exports.data" :key="item.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">#{{ item.id }}</p>
                            </div>
                            <span class="status-pill" :class="item.status === 'success' ? 'is-success' : 'is-failed'">
                                {{ item.status === 'success' ? $t('admin.exportsSuccess') : $t('admin.exportsFailed') }}
                            </span>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.exportsFrom') }}</span>
                                <span class="text-slate-700 text-right">{{ formatSource(item) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.exportsTo') }}</span>
                                <span class="text-slate-700 text-right">{{ item.file_name }}</span>
                            </div>
                            <div v-if="item.download_url" class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('actions.download') }}</span>
                                <a :href="item.download_url" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                                    {{ $t('actions.download') }}
                                </a>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.user') }}</span>
                                <span class="text-slate-700 text-right">{{ item.user?.name || '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('labels.created') }}</span>
                                <span class="text-slate-700 text-right">{{ item.created_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[720px] text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-3 pr-6">ID</th>
                                <th class="pb-3 pr-6">{{ $t('admin.exportsFrom') }}</th>
                                <th class="pb-3 pr-6">{{ $t('admin.exportsTo') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.user') }}</th>
                                <th class="pb-3 pr-6">{{ $t('labels.status') }}</th>
                                <th class="pb-3">{{ $t('labels.created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in exports.data" :key="item.id" class="border-t border-slate-200">
                                <td class="py-4 pr-6 font-semibold text-slate-900">#{{ item.id }}</td>
                                <td class="py-4 pr-6 text-slate-700">{{ formatSource(item) }}</td>
                                <td class="py-4 pr-6 text-slate-600">{{ item.file_name }}</td>
                                <td class="py-4 pr-6 text-slate-600">{{ item.user?.name || '—' }}</td>
                                <td class="py-4 pr-6">
                                    <span class="status-pill" :class="item.status === 'success' ? 'is-success' : 'is-failed'">
                                        {{ item.status === 'success' ? $t('admin.exportsSuccess') : $t('admin.exportsFailed') }}
                                    </span>
                                </td>
                                <td class="py-4 text-slate-600">
                                    <div class="flex items-center gap-3">
                                        <span>{{ item.created_at }}</span>
                                        <a v-if="item.download_url" :href="item.download_url" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                            {{ $t('actions.download') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <Pagination :meta="exports.meta" />
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'AdminExportsIndex',
    layout: AdminLayout,
    components: {
        Head,
        Pagination,
    },
    props: {
        exports: Object,
        types: Array,
        tasks: Array,
        presets: Array,
        users: Array,
    },
    data() {
        return {
            sourceType: 'type',
            sourceId: '',
            userId: '',
            sheetName: '',
            format: 'xlsx',
            selectedColumns: {},
            customLabels: {},
            presetName: '',
            selectedPresetId: '',
        };
    },
    computed: {
        sourceOptions() {
            return this.sourceType === 'type' ? this.types : this.tasks;
        },
        currentSource() {
            return this.sourceOptions.find((item) => String(item.id) === String(this.sourceId));
        },
        currentColumns() {
            return this.currentSource?.template?.columns ?? [];
        },
        canExport() {
            if (!this.sourceId || this.currentColumns.length === 0) {
                return false;
            }
            return this.currentColumns.some((column) => this.selectedColumns[column.id]);
        },
        allSelected() {
            if (this.currentColumns.length === 0) {
                return false;
            }
            return this.currentColumns.every((column) => this.selectedColumns[column.id]);
        },
        selectedPreset() {
            return this.presets.find((preset) => String(preset.id) === String(this.selectedPresetId));
        },
    },
    methods: {
        formatSource(item) {
            if (!item.source_type) {
                return '—';
            }
            return `${item.source_type} #${item.source_id ?? '—'}`;
        },
        toggleColumn(id, checked) {
            this.selectedColumns[id] = checked;
            if (!checked) {
                delete this.customLabels[id];
            }
        },
        toggleAllColumns() {
            if (this.allSelected) {
                this.selectedColumns = {};
                this.customLabels = {};
                return;
            }

            const next = {};
            this.currentColumns.forEach((column) => {
                next[column.id] = true;
            });
            this.selectedColumns = next;
        },
        startExport() {
            if (!this.canExport) {
                return;
            }

            const params = new URLSearchParams();
            params.set('source_type', this.sourceType);
            params.set('source_id', this.sourceId);
            params.set('format', this.format);
            if (this.userId && this.sourceType === 'type') {
                params.set('user_id', this.userId);
            }
            if (this.sheetName.trim() !== '') {
                params.set('sheet_name', this.sheetName.trim());
            }

            this.currentColumns.forEach((column) => {
                if (!this.selectedColumns[column.id]) {
                    return;
                }
                params.append('columns[]', column.id);
                const label = (this.customLabels[column.id] || '').trim();
                if (label !== '') {
                    params.append(`labels[${column.id}]`, label);
                }
            });

            window.location.href = `${route('admin.exports.download')}?${params.toString()}`;
        },
        queueExport() {
            if (!this.canExport) {
                return;
            }

            const payload = this.buildPayload();
            this.$inertia.post(route('admin.exports.queue'), payload, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$inertia.reload({ only: ['exports'] });
                },
            });
        },
        savePreset() {
            if (!this.canExport || this.presetName.trim() === '') {
                return;
            }

            const payload = {
                name: this.presetName.trim(),
                ...this.buildPayload(),
            };

            this.$inertia.post(route('admin.exports.presets.store'), payload, {
                preserveScroll: true,
                onSuccess: () => {
                    this.presetName = '';
                    this.selectedPresetId = '';
                    this.$inertia.reload({ only: ['presets'] });
                },
            });
        },
        deletePreset() {
            if (!this.selectedPresetId) {
                return;
            }

            if (!confirm(this.$t('admin.exportsPresetDeleteConfirm'))) {
                return;
            }

            this.$inertia.delete(route('admin.exports.presets.destroy', this.selectedPresetId), {
                preserveScroll: true,
                onSuccess: () => {
                    this.selectedPresetId = '';
                    this.$inertia.reload({ only: ['presets'] });
                },
            });
        },
        applyPreset() {
            if (!this.selectedPreset) {
                return;
            }

            const preset = this.selectedPreset;
            this.sourceType = preset.source_type;
            this.sourceId = preset.source_id;
            this.format = preset.format || 'xlsx';
            this.userId = preset.filter_user_id || '';
            this.sheetName = preset.sheet_name || '';

            this.$nextTick(() => {
                const nextSelected = {};
                (preset.columns || []).forEach((id) => {
                    nextSelected[id] = true;
                });
                this.selectedColumns = nextSelected;
                this.customLabels = { ...(preset.labels || {}) };
            });
        },
        buildPayload() {
            const payload = {
                source_type: this.sourceType,
                source_id: this.sourceId,
                format: this.format,
                sheet_name: this.sheetName.trim() !== '' ? this.sheetName.trim() : null,
            };

            if (this.userId && this.sourceType === 'type') {
                payload.user_id = this.userId;
            }

            payload.columns = [];
            payload.labels = {};
            this.currentColumns.forEach((column) => {
                if (!this.selectedColumns[column.id]) {
                    return;
                }
                payload.columns.push(column.id);
                const label = (this.customLabels[column.id] || '').trim();
                if (label !== '') {
                    payload.labels[column.id] = label;
                }
            });

            return payload;
        },
    },
    watch: {
        sourceType() {
            this.sourceId = '';
            this.selectedColumns = {};
            this.customLabels = {};
            this.userId = '';
            this.selectedPresetId = '';
        },
        sourceId() {
            this.selectedColumns = {};
            this.customLabels = {};
            this.currentColumns.forEach((column) => {
                this.selectedColumns[column.id] = true;
            });
        },
    },
};
</script>

<style scoped>
.status-pill {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
}

.status-pill.is-success {
    background: rgba(34, 197, 94, 0.15);
    color: #15803d;
}

.status-pill.is-failed {
    background: rgba(239, 68, 68, 0.15);
    color: #b91c1c;
}
</style>
