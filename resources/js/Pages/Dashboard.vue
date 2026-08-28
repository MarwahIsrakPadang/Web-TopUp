<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import DataTable from '@/Components/DataTable.vue'
import Icon from '@/Components/Icon.vue'
import Skeleton from '@/Components/Skeleton.vue'

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    revenueChartData: {
        type: Object,
        default: () => ({}),
    },
    paymentMethodDistribution: {
        type: Array,
        default: () => [],
    },
    recentTransactions: {
        type: Object,
        required: true,
    },
    pendingTransactionsCount: {
        type: Number,
        default: 0,
    },
})

const selectedPeriod = ref('7')
const periodOptions = [
    { value: '7', label: '7 Hari' },
    { value: '30', label: '30 Hari' },
    { value: '90', label: '90 Hari' },
]

const currentChartData = computed(() => {
    return props.revenueChartData[selectedPeriod.value] || []
})

const maxRevenue = computed(() => {
    if (currentChartData.value.length === 0) return 0
    return Math.max(...currentChartData.value.map(d => Number(d.total_revenue || d.amount || 0)))
})

const chartHeight = 200

const chartLabelInterval = computed(() => {
    const count = currentChartData.value.length
    if (count <= 10) return 1
    if (count <= 31) return 5
    return 15
})

const transactionColumns = [
    { key: 'invoice', label: 'Invoice', sortable: true },
    { key: 'game', label: 'Game', sortable: true },
    { key: 'product', label: 'Produk', sortable: true },
    { key: 'payment_method', label: 'Pembayaran', sortable: true },
    { key: 'amount', label: 'Jumlah', sortable: true, render: 'CurrencyCell' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Aksi', render: 'ActionCell' },
]

const currencyFormatter = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
})

function formatCurrency(value) {
    return currencyFormatter.format(Number(value))
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

function handlePageChange(page) {
    // This would typically trigger an Inertia visit with page parameter
    // For now, we'll just emit - the parent would handle it
}

const searchQuery = ref('')
const activeStatusFilter = ref(null)
const isRefreshing = ref(false)

function refreshData() {
    isRefreshing.value = true
    router.reload({
        preserveScroll: true,
        onFinish: () => {
            isRefreshing.value = false
        },
    })
}

const visibleTransactions = computed(() => {
    let data = props.recentTransactions.data

    const query = searchQuery.value.trim().toLowerCase()
    if (query) {
        data = data.filter(t =>
            [t.invoice, t.game, t.product, t.payment_method]
                .some(value => value?.toLowerCase().includes(query))
        )
    }

    if (activeStatusFilter.value) {
        data = data.filter(t => t.status === activeStatusFilter.value)
    }

    return data
})

function handleSearch(query) {
    searchQuery.value = query
}

function handleFilter(filters) {
    activeStatusFilter.value = filters?.status ?? null
}

function handleExport() {
    const headers = ['Invoice', 'Game', 'Produk', 'Pembayaran', 'Jumlah', 'Status', 'Tanggal']
    const rows = visibleTransactions.value.map(t => [
        t.invoice,
        t.game,
        t.product,
        t.payment_method,
        t.amount,
        t.status,
        new Date(t.created_at).toLocaleString('id-ID'),
    ])

    const csv = [headers, ...rows]
        .map(row => row.map(cell => `"${String(cell ?? '').replace(/"/g, '""')}"`).join(';'))
        .join('\n')

    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.download = `transaksi-${new Date().toISOString().slice(0, 10)}.csv`
    link.click()
    URL.revokeObjectURL(link.href)
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-7xl space-y-8 px-6 py-6">
            <div>
                <h1 class="text-3xl font-bold text-[#E0E6ED] tracking-tight">Dashboard</h1>
                <p class="mt-1 text-sm text-[#8899A6]">Ringkasan performa dan aktivitas TopUpGame hari ini.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    label="PESANAN HARI INI"
                    :value="stats.todayOrders?.toLocaleString('id-ID') ?? '0'"
                    :change="stats.ordersChange ?? '+0%'"
                    :change-type="stats.ordersChangeType ?? 'neutral'"
                    icon="shoppingCart"
                />
                <StatCard
                    label="PENDAPATAN HARI INI"
                    :value="formatCurrency(stats.todayRevenue ?? 0)"
                    :change="stats.revenueChange ?? '+0%'"
                    :change-type="stats.revenueChangeType ?? 'neutral'"
                    icon="wallet"
                />
                <StatCard
                    label="TRANSAKSI PENDING"
                    :value="pendingTransactionsCount.toLocaleString('id-ID')"
                    change="Perlu perhatian"
                    change-type="pending"
                    icon="clock"
                    variant="pending"
                    subLabel="Transaksi menunggu verifikasi"
                />
                <StatCard
                    label="TOTAL GAME"
                    :value="stats.activeGames?.toLocaleString('id-ID') ?? '0'"
                    icon="gamepad"
                />
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 rounded-2xl border border-[#222732] bg-[#14171E] p-5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h2 class="text-lg font-semibold text-[#E0E6ED]">Revenue Overview</h2>
                            <p class="mt-0.5 text-xs text-[#8899A6]">Tren pendapatan harian</p>
                        </div>
                        <div class="flex items-center gap-1 bg-[#0A0C10] rounded-lg p-1">
                            <button
                                v-for="period in periodOptions"
                                :key="period.value"
                                @click="selectedPeriod = period.value"
                                class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                                :class="selectedPeriod === period.value
                                    ? 'bg-[#A78BFA] text-white shadow-[0_0_15px_rgba(167,139,250,0.3)]'
                                    : 'text-[#8899A6] hover:text-[#E0E6ED] hover:bg-white/5'"
                            >
                                {{ period.label }}
                            </button>
                        </div>
                    </div>

                    <div v-if="currentChartData.length > 0" class="relative h-[200px]">
                        <div class="pointer-events-none absolute inset-0 flex flex-col justify-between px-2 pb-6">
                            <div
                                v-for="line in 3"
                                :key="line"
                                class="w-full border-t border-dashed"
                                :style="{ borderColor: 'rgba(34, 39, 50, ' + (line === 1 ? 0.9 : 0.5) + ')' }"
                            />
                        </div>
                        <div class="relative flex h-full items-end gap-1.5 px-2">
                            <div
                                v-for="(day, index) in currentChartData"
                                :key="day.date"
                                class="relative flex flex-1 flex-col items-center justify-end min-w-0"
                                :title="formatDate(day.date) + ': ' + formatCurrency(day.total_revenue ?? day.amount ?? 0)"
                            >
                                <div
                                    class="mx-auto w-full max-w-[22px] rounded-t-md transition-all duration-300 group-hover:brightness-125"
                                    :style="{
                                        height: maxRevenue > 0
                                            ? Math.max(((day.total_revenue ?? day.amount ?? 0) / maxRevenue) * (chartHeight - 40), 2) + 'px'
                                            : '2px',
                                        background: 'linear-gradient(180deg, #A78BFA 0%, #8B5CF6 100%)',
                                    }"
                                ></div>
                                <span
                                    class="mt-2 text-[10px] font-medium text-[#8899A6] whitespace-nowrap"
                                    :class="{ 'text-[#E0E6ED] font-semibold': (index % chartLabelInterval === 0 || index === currentChartData.length - 1) }"
                                >
                                    <template v-if="index % chartLabelInterval === 0 || index === currentChartData.length - 1">
                                        {{ new Date(day.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }) }}
                                    </template>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex h-[200px] items-center justify-center">
                        <p class="text-sm text-[#8899A6]">Belum ada data pendapatan untuk periode ini.</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#222732] bg-[#14171E] p-5">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-[#E0E6ED]">Payment Methods</h2>
                        <p class="mt-0.5 text-xs text-[#8899A6]">Distribusi metode pembayaran</p>
                    </div>

                    <div v-if="paymentMethodDistribution.length > 0" class="space-y-4">
                        <div
                            v-for="(method, index) in paymentMethodDistribution"
                            :key="method.name"
                            class="space-y-1.5"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-medium text-white"
                                        :style="{
                                            backgroundColor: ['#A78BFA', '#00C897', '#FBBF24', '#F87171', '#3B82F6', '#EC4899'][index % 6],
                                        }"
                                    >
                                        {{ index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium text-[#E0E6ED]">{{ method.name }}</span>
                                </div>
                                <span class="text-sm font-semibold text-[#A78BFA]">{{ method.percentage }}%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#0A0C10]">
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :style="{
                                        width: method.percentage + '%',
                                        background: 'linear-gradient(90deg, #A78BFA 0%, #8B5CF6 100%)',
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="space-y-3">
                        <div v-for="i in 4" :key="i" class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Skeleton type="circle" height="6" width="6" />
                                    <Skeleton type="text" height="4" width="40" />
                                </div>
                                <Skeleton type="text" height="4" width="30" />
                            </div>
                            <Skeleton type="text" height="1.5" width="full" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-[#222732] bg-[#14171E] overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 border-b border-[#222732]">
                    <div>
                        <h2 class="text-lg font-semibold text-[#E0E6ED]">Recent Transactions</h2>
                        <p class="mt-0.5 text-xs text-[#8899A6]">Transaksi terbaru dari pelanggan.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="flex h-9 items-center justify-center gap-2 rounded-lg border border-[#222732] bg-[#0A0C10] px-3 text-sm font-medium text-[#E0E6ED] transition-all hover:border-[#A78BFA]/50 hover:bg-[#A78BFA]/5 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="isRefreshing"
                            @click="refreshData"
                        >
                            <Icon name="refreshCw" :size="16" :class="{ 'animate-spin': isRefreshing }" />
                            <span>{{ isRefreshing ? 'Memuat...' : 'Refresh' }}</span>
                        </button>
                    </div>
                </div>

                <DataTable
                    :columns="transactionColumns"
                    :data="visibleTransactions"
                    :pagination="{
                        current_page: recentTransactions.current_page,
                        last_page: recentTransactions.last_page,
                        from: recentTransactions.from,
                        to: recentTransactions.to,
                        total: recentTransactions.total,
                        links: recentTransactions.links,
                    }"
                    :loading="false"
                    :searchable="true"
                    :filterable="true"
                    :exportable="true"
                    row-key="id"
                    empty-message="Belum ada transaksi."
                    @search="handleSearch"
                    @filter="handleFilter"
                    @export="handleExport"
                    @page-change="handlePageChange"
                >
                    <template #CurrencyCell="{ value }">
                        <span class="font-medium tabular-nums text-[#E0E6ED]">{{ formatCurrency(value) }}</span>
                    </template>
                    <template #ActionCell="{ row }">
                        <div class="flex items-center justify-end">
                            <button class="flex h-8 w-8 items-center justify-center rounded-lg text-[#8899A6] hover:bg-white/5 hover:text-[#E0E6ED] transition-all" @click.stop="$emit('view-transaction', row)">
                                <Icon name="eye" :size="18" />
                            </button>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>