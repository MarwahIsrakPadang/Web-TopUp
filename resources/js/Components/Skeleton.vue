<script setup>
defineProps({
    type: { type: String, default: 'text' },
    width: { type: String, default: 'full' },
    height: { type: String, default: '4' },
    rounded: { type: String, default: 'md' },
    count: { type: Number, default: 1 },
    class: { type: String, default: '' },
})
</script>

<template>
    <div v-for="i in count" :key="i" :class="class">
        <div
            class="animate-pulse bg-gray-200 dark:bg-gray-700"
            :class="[
                type === 'circle' ? 'rounded-full' : 'rounded-' + rounded,
                type === 'circle' ? 'h-' + height + ' w-' + height : 'h-' + height,
                type === 'text' ? 'w-' + width : '',
                type === 'card' ? 'h-' + height + ' w-full' : '',
            ]"
        />
    </div>
</template>