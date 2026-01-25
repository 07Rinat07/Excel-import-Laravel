<template>
    <div class="mb-4">
        <nav class="bg-gray-800">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">

                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <Link :class="['px-3 py-2 text-sm font-medium', $page.component === 'Project/Index'
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white']" :href="route('project.index')">
                                {{ $t('nav.projects') }}
                            </Link>
                            <Link :class="['px-3 py-2 text-sm font-medium', $page.component === 'Project/Import'
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white']" :href="route('project.import')">
                                {{ $t('nav.imports') }}
                            </Link>
                            <Link :class="['px-3 py-2 text-sm font-medium', $page.component === 'Task/Index'
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white']" :href="route('task.index')">
                                {{ $t('nav.tasks') }}
                            </Link>
                            <Link
                                v-if="$page.props.auth.user && $page.props.auth.user.is_admin"
                                :class="['px-3 py-2 text-sm font-medium', $page.component === 'Admin/Feedback/Index'
                                ? 'bg-gray-900 text-white'
                                : 'text-gray-300 hover:bg-gray-700 hover:text-white']"
                                :href="route('admin.feedback.index')"
                            >
                                {{ $t('nav.feedback') }}
                            </Link>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-3">
                        <LanguageSwitcher />
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-md border border-gray-600 px-3 py-2 text-xs font-semibold text-gray-200 hover:bg-gray-700 hover:text-white"
                        >
                            {{ $t('nav.logout') }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div class="sm:hidden" id="mobile-menu">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <div class="px-2 py-2">
                        <LanguageSwitcher />
                    </div>
                    <Link
                        :href="route('project.index')"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white"
                    >
                        {{ $t('nav.projects') }}
                    </Link>
                    <Link
                        :href="route('project.import')"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white"
                    >
                        {{ $t('nav.imports') }}
                    </Link>
                    <Link
                        :href="route('task.index')"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white"
                    >
                        {{ $t('nav.tasks') }}
                    </Link>
                    <Link
                        v-if="$page.props.auth.user && $page.props.auth.user.is_admin"
                        :href="route('admin.feedback.index')"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white"
                    >
                        {{ $t('nav.feedback') }}
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white"
                    >
                        {{ $t('nav.logout') }}
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>


<script>
import {Link} from "@inertiajs/vue3";
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

export default {
    name: "Navigation",

    components: {
        Link,
        LanguageSwitcher
    }
}
</script>


<style scoped>

</style>
