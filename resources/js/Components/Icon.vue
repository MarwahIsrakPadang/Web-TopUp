<script setup>
import { computed } from 'vue'

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    size: {
        type: [String, Number],
        default: 20,
    },
    class: {
        type: String,
        default: '',
    },
    strokeWidth: {
        type: [String, Number],
        default: 2,
    },
})

const icons = {
    shoppingCart: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='9' cy='21' r='1'/><circle cx='20' cy='21' r='1'/><path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'/></svg>`,
    wallet: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M21 12V7H5V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v5'/><path d='M3 5v14a2 2 0 0 0 2 2h16v-5'/><path d='M18 12a2 2 0 0 0 0-4 2 2 0 0 0 0 4z'/></svg>`,
    clock: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='10'/><polyline points='12 6 12 12 16 14'/></svg>`,
    gamepad: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='6' y1='11' x2='10' y2='11'/><line x1='8' y1='9' x2='8' y2='13'/><line x1='14' y1='11' x2='18' y2='11'/><line x1='16' y1='9' x2='16' y2='13'/><path d='M5 2a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3H5z'/></svg>`,
    trendingUp: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='23 6 13.5 15.5 8.5 10.5 1 18'/><polyline points='17 6 23 6 23 12'/></svg>`,
    trendingDown: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='23 18 13.5 8.5 8.5 13.5 1 6'/><polyline points='17 18 23 18 23 12'/></svg>`,
    search: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='11' cy='11' r='8'/><path d='m21 21-4.3-4.3'/></svg>`,
    filter: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polygon points='22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3'/></svg>`,
    moreHorizontal: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='1'/><circle cx='19' cy='12' r='1'/><circle cx='5' cy='12' r='1'/></svg>`,
    chevronDown: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>`,
    chevronUp: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='18 15 12 9 6 15'/></svg>`,
    chevronLeft: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='15 18 9 12 15 6'/></svg>`,
    chevronRight: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='9 18 15 12 9 6'/></svg>`,
    download: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/><polyline points='7 10 12 15 17 10'/><line x1='12' y1='15' x2='12' y2='3'/></svg>`,
    refreshCw: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8'/><path d='M21 12a9 9 0 1 0-9 9 9.75 9.75 0 0 0 6.74-2.74L21 16'/></svg>`,
    eye: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z'/><circle cx='12' cy='12' r='3'/></svg>`,
    checkCircle: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'/><polyline points='22 4 12 14.01 9 11.01'/></svg>`,
    alertCircle: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='10'/><line x1='12' y1='8' x2='12' y2='12'/><line x1='12' y1='16' x2='12.01' y2='16'/></svg>`,
    xCircle: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='10'/><line x1='15' y1='9' x2='9' y2='15'/><line x1='9' y1='9' x2='15' y2='15'/></svg>`,
    arrowUpRight: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M7 17V7h10'/><polyline points='17 17 7 7 17 7'/></svg>`,
    arrowDownRight: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M7 7v10h10'/><polyline points='17 7 7 17 17 17'/></svg>`,
    activity: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'/></svg>`,
    creditCard: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><rect x='1' y='4' width='22' height='16' rx='2' ry='2'/><line x1='1' y1='10' x2='23' y2='10'/></svg>`,
    qrCode: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><path d='M21 16h-5a2 2 0 0 0-2 2v5'/><path d='M3 16h5a2 2 0 0 1 2 2v5'/></svg>`,
    smartphone: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><rect x='5' y='2' width='14' height='20' rx='2' ry='2'/><line x1='12' y1='18' x2='12.01' y2='18'/></svg>`,
    building: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'/><path d='M3 9h18'/><path d='M3 15h18'/><path d='M9 3v18'/><path d='M15 3v18'/></svg>`,
    users: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 0 0-3-3.87'/><path d='M16 3.13a4 4 0 0 1 0 7.75'/></svg>`,
    settings: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='3'/><path d='M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z'/></svg>`,
    arrowRight: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='5' y1='12' x2='19' y2='12'/><polyline points='12 5 19 12 12 19'/></svg>`,
    arrowLeft: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='19' y1='12' x2='5' y2='12'/><polyline points='12 19 5 12 12 5'/></svg>`,
    menu: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='3' y1='12' x2='21' y2='12'/><line x1='3' y1='6' x2='21' y2='6'/><line x1='3' y1='18' x2='21' y2='18'/></svg>`,
    x: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='6' x2='6' y2='18'/><line x1='6' y1='6' x2='18' y2='18'/></svg>`,
    check: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><polyline points='20 6 9 17 4 12'/></svg>`,
    home: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/><polyline points='9 22 9 12 15 12 15 22'/></svg>`,
    barChart: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='20' x2='18' y2='10'/><line x1='12' y1='20' x2='12' y2='4'/><line x1='6' y1='20' x2='6' y2='14'/></svg>`,
    pieChart: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><path d='M21.21 15.89A10 10 0 1 1 8 2.83'/><path d='M22 12A10 10 0 0 0 12 2v10z'/></svg>`,
    dollarSign: `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='{strokeWidth}' stroke-linecap='round' stroke-linejoin='round'><line x1='12' y1='1' x2='12' y2='23'/><path d='M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'/></svg>`,
}

const svgContent = computed(() => {
    const template = icons[props.name]
    if (!template) {
        return `<svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='${props.strokeWidth}'><text x='12' y='12' text-anchor='middle' font-size='8'>?</text></svg>`
    }
    return template.replace('{strokeWidth}', props.strokeWidth)
})
</script>

<template>
    <div
        :style="{ width: size + 'px', height: size + 'px' }"
        :class="class"
        v-html="svgContent"
        aria-hidden="true"
    />
</template>