<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    banners: { type: Array, required: true },
})

const current = ref(0)
const isPaused = ref(false)
let autoTimer = null

function next() {
    if (props.banners.length === 0) return
    current.value = (current.value + 1) % props.banners.length
}

function prev() {
    if (props.banners.length === 0) return
    current.value = (current.value - 1 + props.banners.length) % props.banners.length
}

function startAutoSlide() {
    stopAutoSlide()
    if (props.banners.length > 1) {
        autoTimer = setInterval(next, 5000)
    }
}

function stopAutoSlide() {
    if (autoTimer) {
        clearInterval(autoTimer)
        autoTimer = null
    }
}

function onMouseEnter() {
    isPaused.value = true
    stopAutoSlide()
}

function onMouseLeave() {
    isPaused.value = false
    startAutoSlide()
}

onMounted(() => startAutoSlide())
onUnmounted(() => stopAutoSlide())
</script>

<template>
    <section
        v-if="banners.length > 0"
        class="relative h-[400px] overflow-hidden bg-gray-900 md:h-[500px]"
        @mouseenter="onMouseEnter"
        @mouseleave="onMouseLeave"
    >
        <div
            v-for="(banner, i) in banners"
            :key="banner.id"
            class="absolute inset-0 transition-opacity duration-500"
            :class="i === current ? 'opacity-100' : 'opacity-0'"
        >
            <img :src="'/storage/' + banner.image" :alt="banner.title" loading="lazy" class="h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white md:p-16">
                <h2 class="text-3xl font-bold md:text-4xl">{{ banner.title }}</h2>
                <p v-if="banner.subtitle" class="mt-2 text-lg text-gray-200">{{ banner.subtitle }}</p>
                <a
                    v-if="banner.link"
                    :href="banner.link"
                    class="mt-4 inline-block rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-500"
                >
                    Lihat Selengkapnya
                </a>
            </div>
        </div>

        <button
            @click="prev"
            class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-black/30 p-2 text-white hover:bg-black/50"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </button>
        <button
            @click="next"
            class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-black/30 p-2 text-white hover:bg-black/50"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        </button>

        <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
            <button
                v-for="(banner, i) in banners"
                :key="banner.id"
                @click="current = i"
                class="h-2 w-2 rounded-full transition-all"
                :class="i === current ? 'w-6 bg-white' : 'bg-white/50'"
            />
        </div>
    </section>

    <section v-else class="flex h-[300px] items-center justify-center bg-gradient-to-br from-primary-600 to-secondary-700 md:h-[400px]">
        <div class="text-center text-white">
            <h1 class="text-4xl font-bold md:text-5xl">TopUpGame</h1>
            <p class="mt-3 text-lg text-primary-200">Top-up game termurah dan terpercaya</p>
        </div>
    </section>
</template>
