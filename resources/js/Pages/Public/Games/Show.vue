<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    game: { type: Object, required: true },
    products: { type: Array, required: true },
    paymentMethods: { type: Array, default: () => [] },
})

const selectedProduct = ref(null)
const selectedChannel = ref(null)

const form = useForm({
    player_id: '',
    zone_id: '',
    product_id: null,
    payment_channel_id: null,
})

function selectProduct(product) {
    selectedProduct.value = product
    form.product_id = product.id
}

function selectPayment(channel) {
    selectedChannel.value = channel
    form.payment_channel_id = channel.id
}

const totalPrice = computed(() => {
    if (!selectedProduct.value) return 0
    const price = Number(selectedProduct.value.price)

    if (selectedChannel.value) {
        if (selectedChannel.value.fee_type === 'percentage') {
            return price + Math.round(price * Number(selectedChannel.value.fee_amount) / 100)
        }
        return price + Number(selectedChannel.value.fee_amount)
    }

    return price
})

const bestSellerProduct = computed(() => {
    const withDiscount = props.products.find(p => p.original_price && Number(p.original_price) > Number(p.price))
    if (withDiscount) return withDiscount.id
    return props.products.length > 0 ? props.products[0].id : null
})

function formatPrice(value) {
    return 'Rp ' + Number(value).toLocaleString('id-ID')
}

const benefits = [
    { icon: 'M12 6v6l4.5 3', label: 'Proses Kilat', desc: 'Saldo masuk dalam hitungan detik setelah pembayaran diverifikasi.' },
    { icon: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z', label: 'Resmi & Aman', desc: 'Bekerja sama langsung dengan developer game resmi. Transaksi dijamin aman.' },
]

const canOrder = computed(() => Boolean(selectedProduct.value && form.payment_channel_id))
</script>

<template>
    <PublicLayout>
        <Head :title="game.name + ' - TopUpGame'">
            <meta name="description" :content="'Top-up ' + game.name + ' murah dan cepat. Pilih paket diamond dan item favorit Anda.'" />
            <meta property="og:title" :content="game.name + ' - TopUpGame'" />
            <meta property="og:description" :content="'Top-up ' + game.name + ' murah dan cepat. Pilih paket diamond dan item favorit Anda.'" />
        </Head>

        <div class="relative overflow-hidden bg-gradient-to-br from-surface to-surface-card">
            <div class="pointer-events-none absolute right-0 top-0 -translate-y-1/2 translate-x-1/2">
                <div class="h-[300px] w-[300px] rounded-full bg-primary-500/5 blur-[100px]" />
            </div>

            <div class="mx-auto max-w-7xl px-4 pb-8 pt-8 sm:px-6 lg:px-8">
                <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                    <div class="relative shrink-0">
                        <div v-if="game.icon" class="h-20 w-20 overflow-hidden rounded-2xl ring-2 ring-primary-500/50 shadow-lg shadow-primary-500/20">
                            <img :src="'/storage/' + game.icon" :alt="game.name" width="80" height="80" fetchpriority="high" class="h-full w-full object-cover" />
                        </div>
                        <div v-else class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-secondary-500 text-3xl font-bold text-white ring-2 ring-primary-500/50 shadow-lg shadow-primary-500/20">
                            {{ game.name.charAt(0) }}
                        </div>
                    </div>

                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white sm:text-3xl">{{ game.name }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full bg-tertiary-500/15 px-3 py-0.5 text-xs font-semibold text-tertiary-500 ring-1 ring-tertiary-500/30">
                                <span class="h-1.5 w-1.5 rounded-full bg-tertiary-500" />
                                Instant Process
                            </span>
                            <span class="rounded-full bg-white/5 px-3 py-0.5 text-xs font-medium text-text-muted ring-1 ring-white/10">
                                MOONTON
                            </span>
                        </div>
                        <p v-if="game.description" class="mt-2 max-w-xl text-sm leading-relaxed" style="color: #94A3B8">{{ game.description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 pb-24 pt-6 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-1">
                    <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                        <h3 class="text-base font-semibold text-white">1. Lengkapi Data</h3>
                        <p class="mt-1 text-xs" style="color: #94A3B8">Masukkan ID player dan zone/server Anda.</p>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="text-xs font-medium text-white">User ID</label>
                                <input
                                    v-model="form.player_id"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="Masukkan User ID"
                                    class="mt-1.5 block w-full rounded-lg border-0 bg-surface px-3.5 py-2.5 text-sm text-white outline-none ring-1 ring-white/10 transition-all placeholder:text-text-muted focus:ring-2 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-white">Zone ID</label>
                                <input
                                    v-model="form.zone_id"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="Masukkan Zone ID (opsional)"
                                    class="mt-1.5 block w-full rounded-lg border-0 bg-surface px-3.5 py-2.5 text-sm text-white outline-none ring-1 ring-white/10 transition-all placeholder:text-text-muted focus:ring-2 focus:ring-primary-500"
                                />
                            </div>
                        </div>
                        <p class="mt-3 text-xs" style="color: #94A3B8">* Pastikan User ID sudah benar. Kesalahan input ID bukan tanggung jawab kami.</p>
                    </div>

                    <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                        <h3 class="text-base font-semibold text-white">Kenapa TopUp di Sini?</h3>
                        <div class="mt-4 space-y-4">
                            <div v-for="(item, i) in benefits" :key="i" class="flex gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-tertiary-500/15">
                                    <svg class="h-4 w-4 text-tertiary-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ item.label }}</p>
                                    <p class="mt-0.5 text-xs leading-relaxed" style="color: #94A3B8">{{ item.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-white">2. Pilih Produk</h3>
                            <span v-if="selectedProduct" class="text-xs" style="color: #94A3B8">{{ selectedProduct.name }} dipilih</span>
                        </div>

                        <div v-if="products.length === 0" class="mt-6 py-8 text-center text-sm" style="color: #94A3B8">
                            Belum ada produk tersedia untuk game ini.
                        </div>

                        <div v-else class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <button
                                v-for="product in products"
                                :key="product.id"
                                @click="selectProduct(product)"
                                class="group relative rounded-xl border-2 p-4 text-left transition-all duration-200"
                                :class="selectedProduct?.id === product.id
                                    ? 'border-primary-500 bg-primary-500/10 shadow-sm shadow-primary-500/20'
                                    : 'border-white/10 bg-surface hover:border-white/20'"
                            >
                                <div v-if="bestSellerProduct === product.id" class="absolute -right-px -top-px rounded-bl-lg rounded-tr-xl bg-tertiary-500 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">
                                    Best Seller
                                </div>
                                <div class="flex items-center gap-3">
                                    <div v-if="product.icon" class="h-9 w-9 shrink-0 overflow-hidden rounded-lg ring-1 ring-white/10">
                                        <img :src="'/storage/' + product.icon" :alt="product.name" width="36" height="36" loading="lazy" decoding="async" class="h-full w-full object-cover" />
                                    </div>
                                    <div v-else class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500/30 to-secondary-500/30 text-xs font-bold text-primary-400 ring-1 ring-white/10">
                                        {{ product.name.charAt(0) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ product.name }}</p>
                                        <p v-if="product.original_price && Number(product.original_price) > Number(product.price)" class="text-xs line-through" style="color: #94A3B8">
                                            {{ formatPrice(product.original_price) }}
                                        </p>
                                        <p class="text-base font-bold text-primary-400">{{ formatPrice(product.price) }}</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                        <h3 class="text-base font-semibold text-white">3. Metode Pembayaran</h3>

                        <div v-if="paymentMethods.length === 0" class="mt-4 py-6 text-center text-sm" style="color: #94A3B8">
                            Belum ada metode pembayaran tersedia.
                        </div>

                        <div v-else class="mt-4 space-y-4">
                            <div v-for="method in paymentMethods" :key="method.id">
                                <p class="text-xs font-medium uppercase tracking-wider text-white/60">{{ method.name }}</p>
                                <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    <button
                                        v-for="channel in method.channels"
                                        :key="channel.id"
                                        type="button"
                                        @click="selectPayment(channel)"
                                        class="flex items-center gap-2 rounded-xl border-2 p-3 transition-all duration-200"
                                        :class="form.payment_channel_id === channel.id
                                            ? 'border-primary-500 bg-primary-500/10'
                                            : 'border-white/10 bg-surface hover:border-white/20'"
                                    >
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-secondary-500 text-xs font-bold text-white">{{ channel.name.charAt(0) }}</span>
                                        <span class="min-w-0 truncate text-sm font-medium text-white">{{ channel.name }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p v-if="selectedChannel" class="mt-3 text-xs" style="color: #94A3B8">
                            Biaya layanan: {{ selectedChannel.fee_type === 'percentage' ? selectedChannel.fee_amount + '%' : formatPrice(selectedChannel.fee_amount) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-white/5 bg-surface/95 backdrop-blur-xl" style="padding-bottom: env(safe-area-inset-bottom);">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <div class="min-w-0">
                    <p class="text-xs" style="color: #94A3B8">Total Pembayaran</p>
                    <p class="truncate text-lg font-bold text-white">{{ selectedProduct ? formatPrice(totalPrice) : '-' }}</p>
                </div>
                <Link
                    :href="canOrder ? route('checkout', { game: game.slug, product: selectedProduct.id }) : '#'"
                    class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-500/30 transition-all hover:from-primary-400 hover:to-secondary-400 sm:px-8"
                    :class="!canOrder ? 'pointer-events-none opacity-50' : ''"
                >
                    Order Now
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
