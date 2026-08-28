<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    game: { type: Object, required: true },
})
</script>

<template>
    <Link
        :href="route('games.show', game.slug)"
        class="group flex flex-col items-center rounded-xl bg-white p-4 shadow-sm transition-all hover:shadow-md hover:-translate-y-1 dark:bg-gray-800"
    >
        <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-700">
            <img
                v-if="game.icon"
                :src="'/storage/' + game.icon"
                :alt="game.name"
                loading="lazy"
                class="h-full w-full object-cover"
            />
            <div v-else class="text-2xl font-bold text-gray-400">
                {{ game.name.charAt(0) }}
            </div>
        </div>
        <h3 class="mt-3 text-center text-sm font-semibold text-gray-900 group-hover:text-primary-600 dark:text-gray-100 dark:group-hover:text-primary-400">
            {{ game.name }}
        </h3>
        <p v-if="game.products_count !== undefined" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ game.products_count }} produk
        </p>
    </Link>
</template>
