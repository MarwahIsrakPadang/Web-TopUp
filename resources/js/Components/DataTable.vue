<script setup>
import { computed, ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import Skeleton from '@/Components/Skeleton.vue'

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    searchable: {
        type: Boolean,
        default: true,
    },
    filterable: {
        type: Boolean,
        default: true,
    },
    exportable: {
        type: Boolean,
        default: true,
    },
    rowKey: {
        type: String,
        default: 'id',
    },
    emptyMessage: {
        type: String,
        default: 'Tidak ada data.',
    },
})

const emit = defineEmits(['search', 'filter', 'export', 'row-click', 'action'])

const searchQuery = ref('')
const showFilters = ref(false)
const sortColumn = ref(null)
const sortDirection = ref('asc')

const sortedData = computed(() => {
    if (!sortColumn.value) return props.data
    return [...props.data].sort((a, b) => {
        const aVal = a[sortColumn.value]
        const bVal = b[sortColumn.value]
        const direction = sortDirection.value === 'asc' ? 1 : -1
        if (aVal < bVal) return -1 * direction
        if (aVal > bVal) return 1 * direction
        return 0
    })
})

function handleSort(column) {
    if (!column.sortable) return
    if (sortColumn.value === column.key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortColumn.value = column.key
        sortDirection.value = 'asc'
    }
}

function handleSearch() {
    emit('search', searchQuery.value)
}

function handleFilter(filters) {
    emit('filter', filters)
}

function handleExport() {
    emit('export')
}

function handleAction(action, row) {
    emit('action', action, row)
}

function getStatusBadge(status) {
    const statusConfig = {
        success: { bg: 'bg-[#00C897]/10', text: 'text-[#00C897]', icon: 'checkCircle', label: 'BERHASIL' },
        pending: { bg: 'bg-[#A78BFA]/10', text: 'text-[#A78BFA]', icon: 'clock', label: 'PENDING' },
        failed: { bg: 'bg-[#F87171]/10', text: 'text-[#F87171]', icon: 'xCircle', label: 'GAGAL' },
        processing: { bg: 'bg-[#FBBF24]/10', text: 'text-[#FBBF24]', icon: 'activity', label: 'DIPROSES' },
        cancelled: { bg: 'bg-[#8899A6]/10', text: 'text-[#8899A6]', icon: 'xCircle', label: 'DIBATALKAN' },
    }
    return statusConfig[status?.toLowerCase()] || statusConfig.pending
}

function formatCurrency(value) {
    return 'Rp ' + Number(value).toLocaleString('id-ID')
}

watch(() => props.data, () => {
    searchQuery.value = ''
})
</script>

<template>
    <div class="space-y-4">
        <div v-if="searchable || filterable || exportable" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div v-if="searchable" class="relative flex-1 max-w-sm">
                <Icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#8899A6]" />
                <input
                    type="text"
                    placeholder="Cari transaksi..."
                    v-model="searchQuery"
                    @input="handleSearch"
                    class="w-full rounded-lg border border-[#222732] bg-[#0A0C10] pl-10 pr-4 py-2 text-sm text-[#E0E6ED] placeholder-[#8899A6] outline-none transition-all focus:border-[#A78BFA]/50 focus:ring-1 focus:ring-[#A78BFA]/20"
                />
            </div>
            <div class="flex items-center gap-2">
                <button
                    v-if="filterable"
                    @click="showFilters = !showFilters"
                    class="flex items-center gap-2 rounded-lg border border-[#222732] bg-[#0A0C10] px-3 py-2 text-sm font-medium text-[#E0E6ED] transition-all hover:border-[#A78BFA]/50 hover:bg-[#A78BFA]/5"
                    :class="{ 'border-[#A78BFA]/50 bg-[#A78BFA]/5': showFilters }"
                >
                    <Icon name="filter" :size="16" />
                    <span>Filter</span>
                    <Icon :name="showFilters ? 'chevronUp' : 'chevronDown'" :size="16" />
                </button>
                <button
                    v-if="exportable"
                    @click="handleExport"
                    class="flex items-center gap-2 rounded-lg border border-[#222732] bg-[#0A0C10] px-3 py-2 text-sm font-medium text-[#E0E6ED] transition-all hover:border-[#A78BFA]/50 hover:bg-[#A78BFA]/5"
                >
                    <Icon name="download" :size="16" />
                    <span>Export</span>
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-[#222732] bg-[#14171E] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#222732]">
                            <th v-for="column in columns" :key="column.key" class="px-4 py-3 text-left">
                                <button
                                    v-if="column.sortable"
                                    @click="handleSort(column)"
                                    class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-[#8899A6] hover:text-[#E0E6ED] transition-colors"
                                >
                                    {{ column.label }}
                                    <Icon
                                        v-if="sortColumn === column.key"
                                        :name="sortDirection === 'asc' ? 'chevronUp' : 'chevronDown'"
                                        :size="12"
                                        class="text-[#A78BFA]"
                                    />
                                    <Icon v-else name="activity" :size="12" class="text-transparent group-hover:text-[#8899A6]" />
                                </button>
                                <span v-else class="text-xs font-semibold uppercase tracking-wider text-[#8899A6]">{{ column.label }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#222732]/50">
                        <template v-if="loading">
                            <tr v-for="i in 5" :key="i">
                                <td v-for="column in columns" :key="column.key" class="px-4 py-4">
                                    <Skeleton type="text" height="4" class="w-full" />
                                </td>
                            </tr>
                        </template>
                        <template v-else-if="sortedData.length === 0">
                            <tr>
                                <td :colspan="columns.length" class="px-4 py-12 text-center text-sm text-[#8899A6]">
                                    {{ emptyMessage }}
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr
                                v-for="row in sortedData"
                                :key="row[rowKey]"
                                class="transition-colors hover:bg-white/[0.02] cursor-pointer"
                                @click="$emit('row-click', row)"
                            >
                                <td v-for="column in columns" :key="column.key" class="px-4 py-3.5">
                                    <template v-if="column.key === 'status'">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            :class="getStatusBadge(row[column.key]).bg + ' ' + getStatusBadge(row[column.key]).text"
                                        >
                                            <Icon :name="getStatusBadge(row[column.key]).icon" :size="10" />
                                            {{ getStatusBadge(row[column.key]).label }}
                                        </span>
                                    </template>
                                    <template v-else-if="column.key === 'amount'">
                                        <span class="font-medium tabular-nums text-[#E0E6ED]">{{ formatCurrency(row[column.key]) }}</span>
                                    </template>
                                    <template v-else-if="column.key === 'actions'">
                                        <Dropdown align="right">
                                            <template #trigger>
                                                <button class="flex h-8 w-8 items-center justify-center rounded-lg text-[#8899A6] hover:bg-white/5 hover:text-[#E0E6ED] transition-all">
                                                    <Icon name="moreHorizontal" :size="18" />
                                                </button>
                                            </template>
                                            <template #content>
                                                <DropdownLink
                                                    v-for="action in column.actions"
                                                    :key="action.key"
                                                    @click.prevent="handleAction(action.key, row)"
                                                    :class="action.danger ? 'text-[#F87171]' : ''"
                                                >
                                                    <Icon :name="action.icon" :size="16" class="mr-2" />
                                                    {{ action.label }}
                                                </DropdownLink>
                                            </template>
                                        </Dropdown>
                                    </template>
                                    <template v-else-if="column.render">
                                        <component :is="column.render" :row="row" :value="row[column.key]" />
                                    </template>
                                    <template v-else>
                                        <div class="text-sm text-[#E0E6ED]">{{ row[column.key] }}</div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div v-if="pagination && pagination.last_page > 1" class="flex items-center justify-between border-t border-[#222732] px-5 py-3">
                <p class="text-sm text-[#8899A6]">
                    Menampilkan {{ pagination.from }}–{{ pagination.to }} dari {{ pagination.total }}
                </p>
                <div class="flex gap-2">
                    <button
                        @click="$emit('page-change', pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium transition-all disabled:opacity-25 disabled:cursor-not-allowed"
                        :class="pagination.current_page <= 1 ? 'text-[#8899A6]' : 'text-[#8899A6] hover:bg-white/5 hover:text-[#E0E6ED]'"
                    >
                        <Icon name="chevronLeft" :size="18" />
                    </button>
                    <button
                        v-for="link in pagination.links"
                        :key="link.url"
                        @click.prevent="$emit('page-change', link.label)"
                        class="flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-sm font-medium transition-all"
                        :class="link.active
                            ? 'bg-[#A78BFA] text-white'
                            : 'text-[#8899A6] hover:bg-white/5 hover:text-[#E0E6ED]'"
                    >
                        {{ link.label }}
                    </button>
                    <button
                        @click="$emit('page-change', pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium transition-all disabled:opacity-25 disabled:cursor-not-allowed"
                        :class="pagination.current_page >= pagination.last_page ? 'text-[#8899A6]' : 'text-[#8899A6] hover:bg-white/5 hover:text-[#E0E6ED]'"
                    >
                        <Icon name="chevronRight" :size="18" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>