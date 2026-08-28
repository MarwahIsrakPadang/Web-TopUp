<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    orders: { type: Object, required: true },
})

const viewState = ref('input')
const playerId = ref('')
const zoneId = ref('')
const searchQuery = ref('')
const filterStatus = ref('all')

const hasOrders = computed(() => props.orders?.data?.length > 0)

function submitId() {
    if (playerId.value.trim()) {
        viewState.value = 'list'
    }
}

function changeId() {
    viewState.value = 'input'
}

const filteredOrders = computed(() => {
    let list = props.orders?.data || []
    if (filterStatus.value !== 'all') {
        list = list.filter(o => o.status === filterStatus.value)
    }
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase()
        list = list.filter(o =>
            o.game_name?.toLowerCase().includes(q) ||
            o.product_name?.toLowerCase().includes(q) ||
            o.invoice_number?.toLowerCase().includes(q)
        )
    }
    return list
})

const statusClasses = {
    pending: 'border-amber-500/30 bg-amber-500/10 text-amber-400',
    paid: 'border-blue-500/30 bg-blue-500/10 text-blue-400',
    processing: 'border-primary-500/30 bg-primary-500/10 text-primary-400',
    success: 'border-tertiary-500/30 bg-tertiary-500/10 text-tertiary-500',
    failed: 'border-red-500/30 bg-red-500/10 text-red-400',
    expired: 'border-gray-500/30 bg-gray-500/10 text-gray-400',
}

const statusLabels = {
    pending: 'Pending',
    paid: 'Paid',
    processing: 'Processing',
    success: 'Success',
    failed: 'Failed',
    expired: 'Expired',
}

const filterTabs = [
    { key: 'all', label: 'All Orders' },
    { key: 'success', label: 'Success' },
    { key: 'pending', label: 'Pending' },
    { key: 'failed', label: 'Failed' },
]

function formatPrice(value) {
    return '$' + Number(value).toFixed(2)
}

function formatDate(date) {
    const d = new Date(date)
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' • ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}

const gameGradients = [
    'from-orange-500 to-red-500',
    'from-blue-500 to-cyan-500',
    'from-teal-500 to-emerald-500',
    'from-purple-500 to-pink-500',
    'from-rose-500 to-red-500',
    'from-yellow-500 to-orange-500',
]
</script>

<template>
    <PublicLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-primary-400">Order Management</p>
                    <h1 class="mt-1 text-3xl font-bold text-white">Order History</h1>
                    <p class="mt-1 text-sm" style="color: #94A3B8">Track your recent game credit purchases, view transaction receipts, and manage your billing history in one place.</p>
                </div>
                <div class="hidden shrink-0 sm:block">
                    <div class="inline-flex items-center gap-1.5 rounded-full border border-tertiary-500/30 bg-tertiary-500/10 px-3 py-1.5 text-xs font-semibold text-tertiary-500">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-tertiary-500 opacity-75" />
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-tertiary-500" />
                        </span>
                        All Systems Nominal
                    </div>
                </div>
            </div>

            <div v-if="viewState === 'input'" class="mt-10">
                <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5 sm:p-8">
                    <h3 class="text-lg font-semibold text-white">Cek Riwayat Pesanan</h3>
                    <p class="mt-1 text-sm" style="color: #94A3B8">Masukkan detail akun game Anda untuk melihat riwayat transaksi.</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-medium text-white">User ID</label>
                            <input
                                v-model="playerId"
                                type="text"
                                placeholder="Contoh: 12345678"
                                class="mt-1.5 block w-full rounded-lg border-0 bg-surface px-3.5 py-2.5 text-sm text-white outline-none ring-1 ring-white/10 transition-all placeholder:text-text-muted focus:ring-2 focus:ring-primary-500"
                            />
                        </div>
                        <div>
                            <label class="text-xs font-medium text-white">Zone ID</label>
                            <input
                                v-model="zoneId"
                                type="text"
                                placeholder="Contoh: 1234"
                                class="mt-1.5 block w-full rounded-lg border-0 bg-surface px-3.5 py-2.5 text-sm text-white outline-none ring-1 ring-white/10 transition-all placeholder:text-text-muted focus:ring-2 focus:ring-primary-500"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            @click="submitId"
                            class="rounded-xl bg-primary-500 px-6 py-2.5 text-sm font-semibold text-white transition-all hover:bg-primary-400"
                        >
                            Lihat Riwayat
                        </button>
                        <p class="text-xs" style="color: #94A3B8">History hanya tersedia untuk game dengan sistem ID (misal: Mobile Legends).</p>
                    </div>
                </div>

                <div class="mt-10 flex flex-col items-center py-12 text-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-surface-card ring-1 ring-white/5">
                        <svg class="h-12 w-12 text-white/10" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-white">Akses Riwayat Dibatasi</h3>
                    <p class="mt-2 max-w-sm text-sm" style="color: #94A3B8">Silakan masukkan User ID dan Zone ID Anda untuk melihat riwayat pesanan Anda.</p>
                    <div class="mt-6 inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-surface-card px-3 py-1.5 text-xs text-text-muted">
                        <svg class="h-3.5 w-3.5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                        Privasi Data Terjamin
                    </div>
                </div>
            </div>

            <div v-else class="mt-8 space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Enter Order ID or Game Name..."
                            class="w-full rounded-lg border-0 bg-surface-card py-2.5 pl-10 pr-4 text-sm text-white outline-none ring-1 ring-white/10 transition-all placeholder:text-text-muted focus:ring-2 focus:ring-primary-500"
                        />
                    </div>
                    <div class="flex gap-1 rounded-lg bg-surface-card p-1 ring-1 ring-white/10">
                        <button
                            v-for="tab in filterTabs"
                            :key="tab.key"
                            @click="filterStatus = tab.key"
                            class="rounded-md px-3 py-1.5 text-xs font-medium transition-all"
                            :class="filterStatus === tab.key ? 'bg-primary-500 text-white' : 'text-text-muted hover:text-white'"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <div v-if="filteredOrders.length === 0" class="flex flex-col items-center py-16 text-center">
                    <svg class="h-16 w-16 text-white/5" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                    <h3 class="mt-4 text-lg font-semibold text-white">No Orders Found</h3>
                    <p class="mt-1 text-sm" style="color: #94A3B8">No transactions match your current filters.</p>
                    <button @click="changeId" class="mt-4 text-sm font-medium text-primary-400 hover:text-primary-300">Change User ID</button>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="order in filteredOrders"
                        :key="order.id"
                        class="flex flex-col gap-4 rounded-xl bg-surface-card p-5 ring-1 ring-white/5 transition-all hover:ring-white/10 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex items-center gap-4">
                            <div v-if="order.game_icon" class="h-12 w-12 shrink-0 overflow-hidden rounded-xl">
                                <img :src="'/storage/' + order.game_icon" :alt="order.game_name" loading="lazy" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br" :class="gameGradients[order.id % gameGradients.length]">
                                <span class="text-lg font-bold text-white/40">{{ (order.game_name || 'G').charAt(0).toUpperCase() }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-white">{{ order.game_name }}</h4>
                                    <span class="text-xs font-semibold text-primary-400">{{ order.product_name }}</span>
                                </div>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs" style="color: #94A3B8">
                                    <span>{{ formatDate(order.created_at) }}</span>
                                    <span class="hidden sm:inline">·</span>
                                    <span class="font-mono">{{ order.invoice_number }}</span>
                                    <span class="hidden sm:inline">·</span>
                                    <span>{{ order.payment_method_name || order.payment_channel_name || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                            <span class="inline-flex rounded-full border px-3 py-0.5 text-xs font-semibold" :class="statusClasses[order.status] || 'border-gray-500/30 bg-gray-500/10 text-gray-400'">
                                {{ statusLabels[order.status] || order.status }}
                            </span>
                            <div class="flex items-center gap-3">
                                <span class="text-base font-bold text-white">{{ formatPrice(order.total_amount) }}</span>
                                <Link :href="route('invoice.show', order.invoice_number)" preserve-scroll class="text-text-muted transition-colors hover:text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" /></svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="orders?.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
                    <template v-for="page in orders.last_page" :key="page">
                        <Link
                            :href="route('orders.index', { page })"
                            preserve-scroll
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium transition-all"
                            :class="page === orders.current_page ? 'bg-primary-500 text-white' : 'bg-surface-card text-text-muted ring-1 ring-white/10 hover:ring-white/20'"
                        >
                            {{ page }}
                        </Link>
                    </template>
                </div>

                <div class="pt-4 text-center">
                    <button @click="changeId" class="text-sm font-medium text-primary-400 transition-colors hover:text-primary-300">
                        &larr; Cari dengan ID lain
                    </button>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
