<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Bar, Pie } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PointElement,
    LineElement
} from 'chart.js';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PointElement,
    LineElement
);

const props = defineProps({
    stats: Object,
    projectsByType: Array,
    importDynamics: Array,
    recentTasks: Array,
});

const barChartData = computed(() => ({
    labels: props.importDynamics.map(d => d.date),
    datasets: [
        {
            label: 'Imports',
            backgroundColor: '#3b82f6',
            data: props.importDynamics.map(d => d.count),
        },
    ],
}));

const pieChartData = computed(() => ({
    labels: props.projectsByType.map(d => d.label),
    datasets: [
        {
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            data: props.projectsByType.map(d => d.count),
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
};

const getStatusClass = (status) => {
    switch (status) {
        case 1: return 'bg-blue-100 text-blue-800';
        case 2: return 'bg-green-100 text-green-800';
        case 3: return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 1: return 'Processing';
        case 2: return 'Success';
        case 3: return 'Error';
        default: return 'Unknown';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <Head :title="$t('labels.dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col space-y-4">
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ $t('labels.dashboard') }}
                </h2>
                <nav class="flex space-x-4">
                    <Link
                        :href="route('project.index')"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                    >
                        {{ $t('nav.projects') }}
                    </Link>
                    <Link
                        :href="route('project.import')"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                    >
                        {{ $t('nav.imports') }}
                    </Link>
                    <Link
                        :href="route('profile.edit')"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                    >
                        {{ $t('nav.profile') }}
                    </Link>
                </nav>
            </div>
        </template>

        <div class="py-12 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-white/20 hover:shadow-md transition-shadow">
                        <div class="text-sm font-bold text-slate-500 uppercase tracking-wider">{{ $t('labels.totalProjects') }}</div>
                        <div class="mt-2 text-4xl font-extrabold text-blue-600">{{ stats.total_projects }}</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-white/20 hover:shadow-md transition-shadow">
                        <div class="text-sm font-bold text-slate-500 uppercase tracking-wider">{{ $t('labels.totalTasks') }}</div>
                        <div class="mt-2 text-4xl font-extrabold text-indigo-600">{{ stats.total_tasks }}</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-white/20 hover:shadow-md transition-shadow">
                        <div class="text-sm font-bold text-green-600 uppercase tracking-wider">{{ $t('labels.successfulImports') }}</div>
                        <div class="mt-2 text-4xl font-extrabold text-green-600">{{ stats.success_tasks }}</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-white/20 hover:shadow-md transition-shadow">
                        <div class="text-sm font-bold text-red-600 uppercase tracking-wider">{{ $t('labels.failedImports') }}</div>
                        <div class="mt-2 text-4xl font-extrabold text-red-600">{{ stats.error_tasks }}</div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-sm border border-white/20">
                        <h3 class="text-xl font-bold mb-6 text-slate-800 flex items-center">
                            <span class="w-2 h-8 bg-blue-500 rounded-full mr-3"></span>
                            {{ $t('labels.importDynamics') }}
                        </h3>
                        <div class="h-72">
                            <Bar v-if="importDynamics.length > 0" :data="barChartData" :options="chartOptions" />
                            <div v-else class="h-full flex items-center justify-center text-slate-400 italic bg-slate-50 rounded-xl">
                                {{ $t('labels.noData') }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-sm border border-white/20">
                        <h3 class="text-xl font-bold mb-6 text-slate-800 flex items-center">
                            <span class="w-2 h-8 bg-indigo-500 rounded-full mr-3"></span>
                            {{ $t('labels.projectsByType') }}
                        </h3>
                        <div class="h-72">
                            <Pie v-if="projectsByType.some(d => d.count > 0)" :data="pieChartData" :options="chartOptions" />
                            <div v-else class="h-full flex items-center justify-center text-slate-400 italic bg-slate-50 rounded-xl">
                                {{ $t('labels.noData') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-white/50">
                        <h3 class="text-xl font-bold text-slate-800 flex items-center">
                            <span class="w-2 h-8 bg-slate-800 rounded-full mr-3"></span>
                            {{ $t('labels.recentTasks') }}
                        </h3>
                        <Link :href="route('task.index')" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold hover:bg-slate-700 transition-colors">
                            {{ $t('actions.viewAll') }}
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-widest font-black">
                                    <th class="px-8 py-4">ID</th>
                                    <th class="px-8 py-4">Type</th>
                                    <th class="px-8 py-4">Status</th>
                                    <th class="px-8 py-4 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="task in recentTasks" :key="task.id" class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-8 py-5 text-sm text-slate-900 font-bold">#{{ task.id }}</td>
                                    <td class="px-8 py-5 text-sm text-slate-600 font-medium">{{ task.type_model?.title || task.type }}</td>
                                    <td class="px-8 py-5">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-black uppercase tracking-tighter shadow-sm', getStatusClass(task.status)]">
                                            {{ getStatusLabel(task.status) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-sm text-slate-500 text-right font-medium">{{ formatDate(task.created_at) }}</td>
                                </tr>
                                <tr v-if="recentTasks.length === 0">
                                    <td colspan="4" class="px-8 py-16 text-center text-slate-400 italic">
                                        {{ $t('labels.noTasks') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
