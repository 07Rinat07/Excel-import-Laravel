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
            <form class="grid gap-6 lg:grid-cols-[1fr_auto]" @submit.prevent="importExcel">
                <div class="space-y-4">
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
                        class="w-full sm:w-auto rounded-full bg-slate-900 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white"
                        type="submit"
                        :disabled="!file || !typeId"
                    >
                        {{ $t('project.importButton') }}
                    </button>
                </div>
            </form>
        </section>

        <div v-if="$page.props.flash.message" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-700">
            {{ $page.props.flash.message }}
        </div>
    </div>
</template>


<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'Import',
    layout: AdminLayout,
    components: {
        Head,
    },
    props: {
        types: Array,
    },
    data() {
        return {
            file: null,
            typeId: '',
        };
    },
    methods: {
        selectExcel() {
            this.$refs.file.click();
        },
        setExcel(event) {
            this.file = event.target.files[0];
        },
        importExcel() {
            if (!this.file || !this.typeId) {
                return;
            }

            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('type_id', this.typeId);

            this.$inertia.post('/projects/import', formData, {
                onSuccess: () => {
                    this.file = null;
                    this.typeId = '';
                    this.$refs.file.value = null;
                },
            });
        },
    },
};
</script>
