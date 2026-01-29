<template>
    <div class="space-y-6">
        <Head :title="$t('labels.importExcel')" />

        <header>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $t('labels.importCenter') }}</p>
            <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $t('project.importTitle') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $t('project.importSubtitle') }}
            </p>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $t('project.importStepsTitle') }}</p>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $t('project.importStepsHeading') }}</h2>
                </div>
                <p class="text-xs text-slate-500">{{ $t('project.importStepsIntro') }}</p>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200/80 p-4">
                    <p class="text-[0.6rem] font-semibold uppercase tracking-[0.3em] text-slate-400">{{ $t('project.importStepsOrder', { step: 1 }) }}</p>
                    <h3 class="mt-2 text-sm font-semibold text-slate-900">{{ $t('project.importStepsItem1Title') }}</h3>
                    <p class="mt-2 text-xs text-slate-500">{{ $t('project.importStepsItem1Text') }}</p>
                </article>
                <article class="rounded-2xl border border-slate-200/80 p-4">
                    <p class="text-[0.6rem] font-semibold uppercase tracking-[0.3em] text-slate-400">{{ $t('project.importStepsOrder', { step: 2 }) }}</p>
                    <h3 class="mt-2 text-sm font-semibold text-slate-900">{{ $t('project.importStepsItem2Title') }}</h3>
                    <p class="mt-2 text-xs text-slate-500">{{ $t('project.importStepsItem2Text') }}</p>
                </article>
                <article class="rounded-2xl border border-slate-200/80 p-4">
                    <p class="text-[0.6rem] font-semibold uppercase tracking-[0.3em] text-slate-400">{{ $t('project.importStepsOrder', { step: 3 }) }}</p>
                    <h3 class="mt-2 text-sm font-semibold text-slate-900">{{ $t('project.importStepsItem3Title') }}</h3>
                    <p class="mt-2 text-xs text-slate-500">{{ $t('project.importStepsItem3Text') }}</p>
                </article>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <form class="grid gap-6 lg:grid-cols-[1fr_auto]" @submit.prevent="importExcel">
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
                            <input @change="setExcel" type="file" ref="file" class="hidden" accept=".xlsx,.csv" />
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
                        {{ loading ? $t('labels.loading') : $t('project.importButton') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-lg shadow-slate-200/40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $t('project.importRecentTitle') }}</p>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $t('project.importRecentSubtitle') }}</h2>
                </div>
                <Link class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-700" :href="route('task.index')">
                    {{ $t('actions.backToTasks') }}
                </Link>
            </div>
            <div class="mt-5 space-y-4">
                <article
                    v-for="task in recentTasks"
                    :key="task.id"
                    class="rounded-2xl border border-slate-200/70 bg-slate-50/80 p-4"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ task.file_title }}</p>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ task.type_title }}</p>
                        </div>
                        <span class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">
                            {{ task.created_at || '-' }}
                        </span>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-4">
                        <span class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-600">
                            {{ statusLabel(task.status_key) }}
                        </span>
                        <Link
                            v-if="task.map_url"
                            class="rounded-full border border-slate-300 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-700"
                            :href="task.map_url"
                        >
                            {{ $t('actions.startImport') }}
                        </Link>
                    </div>
                </article>
                <p v-if="recentTasks.length === 0" class="text-sm text-slate-500">
                    {{ $t('project.importRecentEmpty') }}
                </p>
            </div>
        </section>

        <div v-if="$page.props.flash.message" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-700">
            {{ $page.props.flash.message }}
        </div>
    </div>
</template>


<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'Import',
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
            file: null,
            typeId: '',
            loading: false,
            errorMessage: '',
        };
    },
    methods: {
        selectExcel() {
            this.$refs.file.click();
        },
        setExcel(event) {
            this.file = event.target.files[0];
            this.errorMessage = '';
        },
        async importExcel() {
            if (!this.file || !this.typeId) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const formData = new FormData();
                formData.append('file', this.file);
                formData.append('type_id', this.typeId);

                const response = await axios.post('/projects/import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                    },
                });

                // Redirect к странице сопоставления колонок
                window.location.href = response.data.redirect_url || `/projects/import/${response.data.task_id}/map`;

            } catch (error) {
                console.error('Import error:', error);
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
                this.loading = false;
            }
        },
        statusLabel(status) {
            if (!status) {
                return this.$t('project.importStatus.unknown');
            }

            return this.$t(`project.importStatus.${status}`);
        },
    },
};
</script>
