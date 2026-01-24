<template>
    <div class="max-w-xl rounded-lg bg-white p-6 shadow">
        <Head :title="$t('feedback.title')" />
        <h1 class="text-2xl font-semibold text-gray-900">{{ $t('feedback.title') }}</h1>
        <p class="mt-2 text-gray-600">{{ $t('feedback.subtitle') }}</p>

        <div v-if="$page.props.flash.message" class="mt-4 rounded bg-green-50 p-3 text-green-700">
            {{ $page.props.flash.message }}
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <div>
                <label class="text-sm font-medium text-gray-700" for="name">{{ $t('feedback.name') }}</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 w-full rounded border-gray-300"
                    required
                />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700" for="email">{{ $t('feedback.email') }}</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 w-full rounded border-gray-300"
                    required
                />
                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700" for="message">{{ $t('feedback.message') }}</label>
                <textarea
                    id="message"
                    v-model="form.message"
                    rows="5"
                    class="mt-1 w-full rounded border-gray-300"
                    required
                ></textarea>
                <div v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</div>
            </div>

            <button
                type="submit"
                class="rounded bg-gray-900 px-4 py-2 text-white"
                :disabled="form.processing"
            >
                {{ $t('feedback.send') }}
            </button>
        </form>
    </div>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

export default {
    name: 'FeedbackCreate',
    layout: PublicLayout,
    components: {
        Head,
    },

    setup() {
        const form = useForm({
            name: '',
            email: '',
            message: '',
        });

        const submit = () => {
            form.post(route('feedback.store'), {
                onSuccess: () => form.reset(),
            });
        };

        return { form, submit };
    },
};
</script>
