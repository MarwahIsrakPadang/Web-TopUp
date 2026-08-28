<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    order: { type: Object, required: true },
})

const page = usePage()
const tripay = computed(() => page.props.flash?.tripay ?? null)

const checking = ref(false)
const pollAttempts = ref(0)
const MAX_POLL_ATTEMPTS = 30

function formatPrice(value) {
    return 'Rp ' + Number(value).toLocaleString('id-ID')
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', { dateStyle: 'long', timeStyle: 'short' })
}

const statusLabel = {
    pending: 'Menunggu Pembayaran',
    paid: 'Dibayar',
    processing: 'Diproses',
    success: 'Berhasil',
    failed: 'Gagal',
    expired: 'Kedaluwarsa',
}

const statusColor = {
    pending: 'bg-yellow-500/15 text-yellow-400',
    paid: 'bg-blue-500/15 text-blue-400',
    processing: 'bg-primary-500/15 text-primary-300',
    success: 'bg-green-500/15 text-green-400',
    failed: 'bg-red-500/15 text-red-400',
    expired: 'bg-white/10 text-slate-400',
}

const paymentInstruction = computed(() => {
    if (!tripay.value) return null
    return tripay.value.payment_method ?? null
})

function checkStatus() {
    checking.value = true
    router.get(route('invoice.show', props.order.invoice_number), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['order'],
        onSuccess: () => {
            checking.value = false
        },
        onError: () => {
            checking.value = false
        },
    })
}

function payNow() {
    if (tripay.value?.payment_url) {
        window.open(tripay.value.payment_url, '_blank')
    }
}

const pollingTimer = ref(null)

onMounted(() => {
    if (props.order.status === 'pending') {
        pollingTimer.value = setInterval(() => {
            pollAttempts.value++
            if (pollAttempts.value >= MAX_POLL_ATTEMPTS) {
                clearInterval(pollingTimer.value)
                pollingTimer.value = null
                return
            }
            checkStatus()
        }, 10000)
    }
})

onUnmounted(() => {
    if (pollingTimer.value) {
        clearInterval(pollingTimer.value)
        pollingTimer.value = null
    }
})
</script>

<template>
    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5 sm:p-8">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-500/15">
                        <svg class="h-8 w-8 text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>

                    <h1 class="mt-4 text-2xl font-bold text-white">Pesanan Dibuat!</h1>
                    <p class="mt-2 text-text-muted">Silakan lakukan pembayaran untuk menyelesaikan pesanan.</p>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-white/5 px-4 py-3">
                        <span class="shrink-0 text-sm text-text-muted">No. Invoice</span>
                        <span class="break-all text-right font-mono font-bold text-white">{{ order.invoice_number }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-white/5 px-4 py-3">
                        <span class="text-sm text-text-muted">Status</span>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="statusColor[order.status]">
                            {{ statusLabel[order.status] }}
                        </span>
                    </div>

                    <div class="rounded-lg bg-white/5 p-4">
                        <div class="flex justify-between gap-3 text-sm">
                            <span class="shrink-0 text-text-muted">Game</span>
                            <span class="text-right font-medium text-white">{{ order.game_name }}</span>
                        </div>
                        <div class="mt-2 flex justify-between gap-3 text-sm">
                            <span class="shrink-0 text-text-muted">Produk</span>
                            <span class="text-right font-medium text-white">{{ order.product_name }}</span>
                        </div>
                        <div class="mt-2 flex justify-between gap-3 text-sm">
                            <span class="shrink-0 text-text-muted">ID Player</span>
                            <span class="text-right font-medium text-white">{{ order.player_id }}<span v-if="order.player_server"> ({{ order.player_server }})</span></span>
                        </div>
                        <div class="mt-2 flex justify-between gap-3 text-sm">
                            <span class="shrink-0 text-text-muted">Pembayaran</span>
                            <span class="text-right font-medium text-white">{{ order.payment_method_name }} — {{ order.payment_channel_name }}</span>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white/5 p-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-text-muted">Harga</span>
                            <span class="text-white">{{ formatPrice(order.amount) }}</span>
                        </div>
                        <div v-if="order.discount_amount > 0" class="mt-2 flex justify-between text-sm">
                            <span class="text-green-600">Diskon</span>
                            <span class="font-medium text-green-600">-{{ formatPrice(order.discount_amount) }}</span>
                        </div>
                        <div class="mt-2 flex justify-between text-sm">
                            <span class="text-text-muted">Biaya</span>
                            <span class="text-white">{{ formatPrice(order.fee) }}</span>
                        </div>
                        <div class="mt-3 flex justify-between border-t border-white/10 pt-3">
                            <span class="font-semibold text-white">Total</span>
                            <span class="text-xl font-bold text-primary-400">{{ formatPrice(order.total_amount) }}</span>
                        </div>
                    </div>

                    <div v-if="order.status === 'pending'" class="rounded-lg border-2 border-dashed border-yellow-500/40 bg-yellow-500/10 p-4 text-center">
                        <p class="text-sm text-yellow-400">Segera lakukan pembayaran sebelum pesanan kedaluwarsa.</p>
                        <p class="mt-1 text-xs text-yellow-400/70">Halaman ini akan otomatis memperbarui status setiap 10 detik.</p>
                    </div>

                    <div v-if="tripay && order.status === 'pending'" class="rounded-lg bg-primary-500/10 p-4">
                        <h3 class="font-semibold text-primary-300">Instruksi Pembayaran</h3>
                        <p class="mt-1 text-sm text-primary-400">Klik tombol di bawah untuk melanjutkan pembayaran melalui Tripay.</p>
                        <button
                            @click="payNow"
                            class="mt-4 w-full rounded-lg bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-primary-500"
                        >
                            Bayar Sekarang
                        </button>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <Link :href="route('home')" class="text-sm font-medium text-primary-400 hover:text-primary-300">
                        Kembali ke Beranda
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
