<template>
    <div class="space-y-8">
        <Head :title="$t('admin.typesTitle')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('admin.typesTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('admin.typesSubtitle') }}</p>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.typesAdd') }}</p>
                    <p class="text-sm text-slate-600">{{ $t('admin.typesAddHint') }}</p>
                </div>
                <form class="flex flex-wrap items-center gap-3" @submit.prevent="submit">
                    <input
                        v-model="title"
                        type="text"
                        class="w-64 rounded-xl border border-slate-200 px-4 py-2 text-sm"
                        :placeholder="$t('admin.typesNamePlaceholder')"
                    />
                    <input
                        v-model="description"
                        type="text"
                        class="w-80 rounded-xl border border-slate-200 px-4 py-2 text-sm"
                        :placeholder="$t('admin.typesDescriptionPlaceholder')"
                    />
                    <button
                        type="submit"
                        class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white"
                        :disabled="!title.trim()"
                    >
                        {{ $t('actions.add') }}
                    </button>
                </form>
            </div>

            <div v-if="$page.props.errors?.title" class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700">
                {{ $page.props.errors.title }}
            </div>
            <div v-if="$page.props.flash?.message" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">
                {{ $page.props.flash.message }}
            </div>

            <div class="mt-6">
                <div v-if="types.length === 0" class="text-center text-sm text-slate-600">
                    {{ $t('admin.typesEmpty') }}
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-[640px] text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-3 pr-6">{{ $t('admin.typesName') }}</th>
                                <th class="pb-3 pr-6">{{ $t('admin.typesDescription') }}</th>
                                <th class="pb-3 pr-6">{{ $t('admin.typesUsage') }}</th>
                                <th class="pb-3 text-right">{{ $t('labels.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="type in types" :key="type.id" class="border-t border-slate-200">
                                <td class="py-4 pr-6">
                                    <p class="font-semibold text-slate-900">{{ type.title }}</p>
                                    <p class="text-xs text-slate-500">ID: {{ type.id }}</p>
                                </td>
                                <td class="py-4 pr-6 text-slate-600">
                                    <div class="flex flex-col gap-2">
                                        <input
                                            v-model="editDescriptions[type.id]"
                                            type="text"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs"
                                            :placeholder="$t('admin.typesDescriptionPlaceholder')"
                                            @blur="saveDescription(type)"
                                        />
                                        <span
                                            v-if="savedTypeId === type.id"
                                            class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-emerald-600"
                                        >
                                            {{ $t('admin.typesSaved') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 pr-6 text-slate-600">
                                    {{ type.projects_count }} {{ $t('labels.projects') }},
                                    {{ type.tasks_count }} {{ $t('labels.tasks') }},
                                    {{ type.templates_count }} {{ $t('labels.templates') }}
                                </td>
                                <td class="py-4 text-right">
                                    <button
                                        class="rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600"
                                        type="button"
                                        @click="destroy(type)"
                                    >
                                        {{ $t('actions.delete') }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'AdminTypesIndex',
    layout: AdminLayout,
    components: {
        Head,
    },
    props: {
        types: Array,
    },
    data() {
        return {
            title: '',
            description: '',
            editDescriptions: {},
            originalDescriptions: {},
            savedTypeId: null,
        };
    },
    mounted() {
        this.resetDescriptions();
    },
    methods: {
        submit() {
            this.$inertia.post(route('admin.types.store'), {
                title: this.title,
                description: this.description,
            }, {
                onSuccess: () => {
                    this.title = '';
                    this.description = '';
                },
            });
        },
        resetDescriptions() {
            const next = {};
            const originals = {};
            this.types.forEach((type) => {
                const value = type.description || '';
                next[type.id] = value;
                originals[type.id] = value;
            });
            this.editDescriptions = next;
            this.originalDescriptions = originals;
        },
        saveDescription(type) {
            const current = this.editDescriptions[type.id] ?? '';
            if (current === (this.originalDescriptions[type.id] ?? '')) {
                return;
            }
            this.$inertia.patch(route('admin.types.update', type.id), {
                description: current,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.resetDescriptions();
                    this.savedTypeId = type.id;
                    setTimeout(() => {
                        if (this.savedTypeId === type.id) {
                            this.savedTypeId = null;
                        }
                    }, 1500);
                },
            });
        },
        destroy(type) {
            if (!confirm(this.$t('admin.typesDeleteConfirm', { title: type.title }))) {
                return;
            }
            this.$inertia.delete(route('admin.types.destroy', type.id));
        },
    },
};
</script>
