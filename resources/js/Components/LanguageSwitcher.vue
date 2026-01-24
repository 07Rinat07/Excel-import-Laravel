<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const locales = computed(() => page.props.locales || []);
const current = computed(() => page.props.locale || 'en');
</script>

<template>
    <div class="lang-switch">
        <span class="lang-label">{{ $t('labels.language') }}</span>
        <div class="lang-buttons">
            <Link
                v-for="locale in locales"
                :key="locale.key"
                method="post"
                as="button"
                class="lang-button"
                :class="{ active: current === locale.key }"
                :href="route('locale.set', { locale: locale.key })"
            >
                {{ locale.label }}
            </Link>
        </div>
    </div>
</template>

<style scoped>
.lang-switch {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.lang-label {
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(15, 23, 42, 0.6);
    font-weight: 600;
}

.lang-buttons {
    display: inline-flex;
    gap: 0.35rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 999px;
    padding: 0.2rem;
    border: 1px solid rgba(15, 23, 42, 0.12);
}

.lang-button {
    border: none;
    background: transparent;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #0f172a;
    cursor: pointer;
}

.lang-button.active {
    background: #0f172a;
    color: #ffffff;
}
</style>
