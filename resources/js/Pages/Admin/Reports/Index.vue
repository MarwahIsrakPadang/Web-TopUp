<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    filters: { type: Object, default: () => ({}) },
    summary: { type: Object, required: true },
    dailySales: { type: Array, default: () => [] },
    topGames: { type: Array, default: () => [] },
})

const form = useForm({
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
})

function formatPrice(value) {
    return 'Rp ' + Number(value).toLocaleString('id-ID')
}

function filter() {
    form.get(route('admin.reports.index'), {
        preserveState: true,
        preserveScroll: true,
    })
}

const maxRevenue = computed(() => {
    if (props.dailySales.length === 0) return 0
    return Math.max(...props.dailySales.map(d => Number(d.total_revenue)))
})

const chartHeight = 180
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-6xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Laporan Penjualan</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ringkasan penjualan dan pendapatan.</p>
                </div>
                <Link
                    :href="route('admin.reports.export', form.data())"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500"
                >
                    Export PDF
                </Link>
            </div>

            <form @submit.prevent="filter" class="mt-6 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari</label>
                    <input
                        v-model="form.start_date"
                        type="date"
                        class="mt-1 block rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai</label>
                    <input
                        v-model="form.end_date"
                        type="date"
                        class="mt-1 block rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                </div>
                <button
                    type="submit"
                    class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-500"
                >
                    Filter
                </button>
            </form>

            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pesanan</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ summary.total_orders }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                    <p class="mt-2 text-3xl font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(summary.total_revenue) }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Biaya + Pending</p>
                    <p class="mt-2 text-lg font-bold text-gray-900 dark:text-gray-100">Biaya: {{ formatPrice(summary.total_fee) }}</p>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400">Pending: {{ summary.pending_count }} pesanan</p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Penjualan Harian</h2>
                    <div v-if="dailySales.length > 0" class="mt-4" :style="{ height: chartHeight + 'px' }">
                        <div class="flex h-full items-end gap-1">
                            <div
                                v-for="day in dailySales"
                                :key="day.date"
                                class="relative flex flex-1 flex-col items-center justify-end"
                                :title="day.date + ': ' + formatPrice(day.total_revenue)"
                            >
                                <div
                                    class="w-full rounded-t bg-primary-500 transition-all hover:bg-primary-600"
                                    :style="{ height: (day.total_revenue / maxRevenue) * (chartHeight - 20) + 'px' }"
                                ></div>
                                <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 rotate-45 origin-left whitespace-nowrap">
                                    {{ day.date.slice(5) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <p v-else class="mt-4 text-sm text-gray-500 dark:text-gray-400">Belum ada data penjualan.</p>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Top Game</h2>
                    <div v-if="topGames.length > 0" class="mt-4 space-y-3">
                        <div
                            v-for="(game, index) in topGames"
                            :key="game.game_name"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    {{ index + 1 }}
                                </span>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ game.game_name }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatPrice(game.total_revenue) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ game.total_orders }} pesanan</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="mt-4 text-sm text-gray-500 dark:text-gray-400">Belum ada data game.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
