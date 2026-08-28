<script setup>
import Icon from '@/Components/Icon.vue'

defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    change: {
        type: String,
        default: '',
    },
    changeType: {
        type: String,
        default: 'neutral',
        validator: (value) => ['up', 'down', 'neutral', 'pending'].includes(value),
    },
    icon: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'pending'].includes(value),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    subLabel: {
        type: String,
        default: '',
    },
})

const changeColors = {
    up: 'text-[#00C897]',
    down: 'text-[#F87171]',
    neutral: 'text-[#8899A6]',
    pending: 'text-[#A78BFA]',
}

const changeIcons = {
    up: 'trendingUp',
    down: 'trendingDown',
    neutral: 'activity',
    pending: 'clock',
}

const iconBgColors = {
    default: 'bg-gradient-to-br from-[#A78BFA]/30 to-[#8B5CF6]/30',
    pending: 'bg-gradient-to-br from-[#F87171]/30 to-[#F87171]/30',
}

const iconTextColors = {
    default: 'text-[#A78BFA]',
    pending: 'text-[#F87171]',
}

const accentColors = {
    default: 'linear-gradient(90deg, transparent, #A78BFA, #8B5CF6, transparent)',
    pending: 'linear-gradient(90deg, transparent, #F87171, transparent)',
}
</script>

<template>
    <div
        class="group relative rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.3)] hover:-translate-y-0.5"
        :class="{
            'bg-[#14171E] border border-[#222732]': variant === 'default',
            'bg-[#14171E] border border-[#F87171]/20': variant === 'pending',
        }"
    >
        <span
            class="pointer-events-none absolute inset-x-4 top-0 h-px opacity-40 transition-all duration-300 group-hover:inset-x-0 group-hover:opacity-100"
            :style="{ background: accentColors[variant] }"
        />
        <div class="flex items-center justify-between mb-4">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl"
                :class="[iconBgColors[variant], iconTextColors[variant]]"
            >
                <Icon :name="icon" :size="20" />
            </div>
            <div v-if="change" class="flex items-center gap-1 text-xs font-medium" :class="changeColors[changeType]">
                <Icon :name="changeIcons[changeType]" :size="12" />
                <span>{{ change }}</span>
            </div>
        </div>

        <div class="space-y-1">
            <p class="text-xs font-medium text-[#8899A6]">{{ label }}</p>
            <div v-if="loading" class="h-8 w-3/4 animate-pulse rounded bg-[#222732]" />
            <div v-else class="text-3xl font-bold text-[#E0E6ED] tabular-nums">{{ value }}</div>
        </div>

        <div v-if="subLabel" class="mt-3 pt-3 border-t border-[#222732]">
            <p class="text-xs text-[#8899A6]">{{ subLabel }}</p>
        </div>
    </div>
</template>