<template>
    <div class="space-y-8">
        <Head :title="$t('admin.templatesTitle')" />

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('admin.templatesTitle') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $t('admin.templatesSubtitle') }}</p>
            </div>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-lg shadow-slate-200/50">
            <div v-if="templates.data.length === 0" class="text-center text-sm text-slate-600">
                {{ $t('admin.templatesEmpty') }}
            </div>
            <div v-else class="space-y-6">
                <div class="space-y-4 sm:hidden">
                    <div v-for="template in templates.data" :key="template.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">ID</p>
                                <p class="text-lg font-semibold text-slate-900">{{ template.name }}</p>
                                <p class="text-xs text-slate-500">#{{ template.id }}</p>
                            </div>
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                :class="template.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'"
                            >
                                {{ template.is_active ? $t('admin.templatesActiveYes') : $t('admin.templatesActiveNo') }}
                            </span>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.templatesType') }}</span>
                                <span class="text-slate-700 text-right">{{ template.type ? template.type.title : $t('admin.templatesNoType') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.templatesColumns') }}</span>
                                <span class="text-slate-700 text-right">{{ template.columns_count }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $t('admin.templatesUpdated') }}</span>
                                <span class="text-slate-700 text-right">{{ template.updated_at || '—' }}</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <Link class="w-full rounded-full bg-slate-900 px-4 py-2 text-center text-xs font-semibold text-white" :href="route('admin.templates.edit', template.id)">
                                {{ $t('actions.edit') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-[720px] text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3 pr-6">{{ $t('admin.templatesName') }}</th>
                            <th class="pb-3 pr-6">{{ $t('admin.templatesType') }}</th>
                            <th class="pb-3 pr-6">{{ $t('admin.templatesColumns') }}</th>
                            <th class="pb-3 pr-6">{{ $t('labels.status') }}</th>
                            <th class="pb-3 pr-6">{{ $t('admin.templatesUpdated') }}</th>
                            <th class="pb-3 text-right">{{ $t('labels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="template in templates.data" :key="template.id" class="border-t border-slate-200">
                            <td class="py-4 pr-6">
                                <p class="font-semibold text-slate-900">{{ template.name }}</p>
                                <p class="text-xs text-slate-500">ID: {{ template.id }}</p>
                            </td>
                            <td class="py-4 pr-6">
                                <span class="text-slate-700">
                                    {{ template.type ? template.type.title : $t('admin.templatesNoType') }}
                                </span>
                            </td>
                            <td class="py-4 pr-6">
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ template.columns_count }}
                                </span>
                            </td>
                            <td class="py-4 pr-6">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="template.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'"
                                >
                                    {{ template.is_active ? $t('admin.templatesActiveYes') : $t('admin.templatesActiveNo') }}
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-slate-600">
                                {{ template.updated_at || '—' }}
                            </td>
                            <td class="py-4 text-right">
                                <Link class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white" :href="route('admin.templates.edit', template.id)">
                                    {{ $t('actions.edit') }}
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </section>

        <Pagination :meta="templates.meta" />
    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'AdminTemplatesIndex',
    layout: AdminLayout,
    components: {
        Head,
        Link,
        Pagination,
    },
    props: {
        templates: Object,
    },
};
</script>
