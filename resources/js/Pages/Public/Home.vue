<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import TiltedCard from '@/Components/TiltedCard.vue'
import { usePrefetch } from '@/Composables/usePrefetch.js'

const props = defineProps({
    games: { type: Array, required: true },
})

const { prefetch } = usePrefetch()

const searchQuery = ref('')
const showAllGames = ref(false)

const filteredGames = computed(() => {
    if (!props.games || props.games.length === 0) return []
    if (!searchQuery.value.trim()) return props.games
    const q = searchQuery.value.toLowerCase()
    return props.games.filter(g => g.name.toLowerCase().includes(q))
})

const displayGames = computed(() => {
    if (showAllGames.value || searchQuery.value.trim()) return filteredGames.value
    return filteredGames.value.slice(0, 8)
})

const hasMoreGames = computed(() => {
    if (searchQuery.value.trim()) return false
    return filteredGames.value.length > 8
})

const gradients = [
    'from-orange-500 to-red-600',
    'from-blue-500 to-cyan-500',
    'from-teal-500 to-emerald-500',
    'from-yellow-500 to-orange-500',
    'from-purple-500 to-pink-500',
    'from-rose-500 to-red-500',
]

function handleHeroSearch() {
    if (searchQuery.value.trim()) {
        document.getElementById('games')?.scrollIntoView({ behavior: 'smooth' })
    }
}

function toggleShowAll() {
    showAllGames.value = !showAllGames.value
}

const steps = [
    {
        icon: 'M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z',
        title: 'Pilih Game',
        desc: 'Cari dan pilih game favorit yang ingin kamu top-up.',
    },
    {
        icon: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
        title: 'Masukkan ID',
        desc: 'Isi User ID dan nominal yang ingin dibeli dengan benar.',
    },
    {
        icon: 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
        title: 'Bayar & Selesai',
        desc: 'Pilih metode bayar, selesaikan, dan saldo masuk instan.',
    },
]

const stats = [
    { value: '1M+', label: 'Transaksi', color: 'from-primary-500 to-secondary-500' },
    { value: '24/7', label: 'Support', color: 'from-secondary-500 to-purple-400' },
    { value: '99.9%', label: 'Keamanan', color: 'from-tertiary-500 to-emerald-400' },
    { value: '100+', label: 'Metode Bayar', color: 'from-primary-500 to-tertiary-500' },
]
</script>

<template>
    <PublicLayout>
        <Head title="TopUpGame - Top-Up Game Termurah & Terpercaya">
            <meta name="description" content="TopUpGame adalah platform top-up game termurah dan terpercaya. Isi diamond, voucher, dan item game favorit Anda dengan harga terbaik." />
            <meta property="og:title" content="TopUpGame - Top-Up Game Termurah & Terpercaya" />
            <meta property="og:description" content="Platform top-up game termurah dan terpercaya. Isi diamond, voucher, dan item game favorit Anda." />
            <meta property="og:type" content="website" />
            <meta name="twitter:card" content="summary_large_image" />
        </Head>

        <section class="relative overflow-hidden px-4 pb-20 pt-16 sm:px-6 lg:px-8">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary-500/10 via-transparent to-transparent" />
            <div class="pointer-events-none absolute left-1/2 top-0 -translate-x-1/2">
                <div class="h-[400px] w-[600px] rounded-full bg-primary-500/5 blur-[120px]" />
            </div>

            <div class="relative mx-auto max-w-4xl text-center">
                <div class="mb-6 inline-flex items-center gap-1.5 rounded-full bg-tertiary-500 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-white shadow-lg shadow-tertiary-500/25">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75" />
                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-white" />
                    </span>
                    Sistem Otomatis 24 Jam
                </div>

                <h1 class="text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                    Top-up Game<br />
                    <span class="bg-gradient-to-r from-primary-500 to-secondary-500 bg-clip-text text-transparent">Tercepat & Terpercaya</span>
                </h1>

                <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed" style="color: #94A3B8">
                    Nikmati pengalaman top-up instan dengan sistem pembayaran terlengkap dan keamanan transaksi 100% terjamin di platform gaming premium Indonesia.
                </p>

                <div class="mx-auto mt-10 max-w-xl">
                    <div class="flex flex-col gap-2 rounded-2xl bg-surface-card p-2 ring-1 ring-white/10 transition-all focus-within:ring-primary-500/50 sm:flex-row">
                        <div class="flex-1">
                            <input
                                v-model="searchQuery"
                                @keyup.enter="handleHeroSearch"
                                type="text"
                                placeholder="Cari game favorit Anda (e.g. Mobile Legends, Valorant...)"
                                class="w-full bg-transparent px-3 py-2.5 text-sm text-white outline-none" style="color-scheme: dark;"
                            />
                        </div>
                        <button
                            @click="handleHeroSearch"
                            class="rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-6 py-2.5 text-sm font-semibold text-white transition-all hover:from-primary-400 hover:to-secondary-400 sm:w-auto"
                        >
                            Cari Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section id="games" class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white">Pilih Game</h2>
                        <p class="mt-2" style="color: #94A3B8">Tersedia lebih dari 100+ game populer favoritmu</p>
                    </div>
                    <button @click="toggleShowAll" class="hidden items-center gap-1 text-sm font-semibold text-primary-400 transition-colors hover:text-primary-300 sm:flex">
                        {{ showAllGames ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua' }}
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-90': showAllGames }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>

                <div v-if="filteredGames.length === 0" class="mt-12 text-center text-sm" style="color: #94A3B8">
                    Game tidak ditemukan.
                </div>

                <div v-else class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 md:grid-cols-4">
                    <Link
                        v-for="(game, index) in displayGames"
                        :key="game.id"
                        :href="route('games.show', { slug: game.slug })"
                        @mouseenter="prefetch(route('games.show', { slug: game.slug }))"
                        class="block rounded-xl overflow-hidden"
                        style="aspect-ratio: 3/4;"
                    >
                        <TiltedCard
                            :imageSrc="game.icon ? '/storage/' + game.icon : ''"
                            :altText="game.name"
                            :captionText="game.name"
                            containerHeight="100%"
                            containerWidth="100%"
                            imageHeight="100%"
                            imageWidth="100%"
                            :rotateAmplitude="12"
                            :scaleOnHover="1.05"
                            :showMobileWarning="false"
                            :showTooltip="false"
                            :displayOverlayContent="true"
                        >
                            <template #placeholder>
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br" :class="gradients[index % gradients.length]">
                                    <span class="select-none text-5xl font-bold text-white/20">{{ game.name.charAt(0) }}</span>
                                </div>
                            </template>
                            <template #overlay>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-surface-card via-surface-card/95 to-transparent p-4 pt-8" style="pointer-events: none;">
                                    <h3 class="font-semibold text-white" style="pointer-events: none;">{{ game.name }}</h3>
                                    <p class="mt-0.5 text-xs" style="color: #94A3B8; pointer-events: none;">{{ game.products_count || 0 }} produk</p>
                                </div>
                            </template>
                        </TiltedCard>
                    </Link>
                </div>

                <button v-if="hasMoreGames || showAllGames" @click="toggleShowAll" class="mt-6 flex w-full items-center justify-center gap-1 text-sm font-semibold text-primary-400 transition-colors hover:text-primary-300 sm:hidden">
                    {{ showAllGames ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua Game' }}
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-90': showAllGames }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </button>
            </div>
        </section>

        <section class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-white">Cara Order</h2>
                    <p class="mt-2" style="color: #94A3B8">Hanya butuh beberapa detik untuk melakukan pengisian saldo game favoritmu.</p>
                </div>

                <div class="relative mt-16 grid gap-8 md:grid-cols-3">
                    <div class="absolute left-[15%] right-[15%] top-12 hidden border-t-2 border-dashed border-white/10 md:block" />

                    <div v-for="(step, i) in steps" :key="i" class="relative flex flex-col items-center text-center">
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-500/10 ring-1 ring-primary-500/30">
                            <svg class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="step.icon" />
                            </svg>
                        </div>
                        <div class="mt-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary-500 text-xs font-bold text-white">{{ i + 1 }}</div>
                        <h3 class="mt-4 text-lg font-semibold text-white">{{ step.title }}</h3>
                        <p class="mt-2 max-w-xs text-sm" style="color: #94A3B8">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 pb-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl bg-surface-card p-8 ring-1 ring-white/5">
                        <h3 class="text-2xl font-bold text-white">Gak Mau Ketinggalan Promo?</h3>
                        <p class="mt-3 leading-relaxed" style="color: #94A3B8">Berlangganan newsletter kami untuk mendapatkan update event game dan promo diskon mingguan langsung di emailmu.</p>
                        <div class="mt-6 flex gap-2">
                            <input
                                type="email"
                                placeholder="Masukkan email Anda"
                                class="flex-1 rounded-xl bg-surface px-4 py-3 text-sm text-white outline-none ring-1 ring-white/10 transition-all focus:ring-primary-500/50"
                            />
                            <button class="rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white transition-all hover:bg-primary-400">
                                Daftar
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="(stat, i) in stats" :key="i" class="rounded-2xl bg-surface-card p-6 ring-1 ring-white/5">
                            <p class="text-3xl font-bold bg-gradient-to-r bg-clip-text text-transparent" :class="stat.color">
                                {{ stat.value }}
                            </p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wider" style="color: #94A3B8">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
