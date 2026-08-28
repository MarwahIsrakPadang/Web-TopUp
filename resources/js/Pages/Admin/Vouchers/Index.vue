<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
    vouchers: { type: Object, required: true },
})

const showConfirmModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

function destroy(id) {
    itemToDelete.value = id
    showConfirmModal.value = true
}

function confirmDelete() {
    deleting.value = true
    router.delete(route('admin.vouchers.destroy', itemToDelete.value), {
        onFinish: () => {
            showConfirmModal.value = false
            deleting.value = false
            itemToDelete.value = null
        },
    })
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', { dateStyle: 'medium' })
}

function isExpired(voucher) {
    return new Date(voucher.end_date) < new Date()
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-7xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Voucher</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Kelola kode diskon untuk pelanggan.</p>
                </div>
                <Link
                    :href="route('admin.vouchers.create')"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500"
                >
                    + Tambah Voucher
                </Link>
            </div>

            <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Diskon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Min Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Pemakaian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="v in vouchers.data" :key="v.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono font-bold text-gray-900 dark:text-gray-100">{{ v.code }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-900/50 dark:text-primary-300">
                                    {{ v.type === 'percentage' ? v.amount + '%' : 'Rp ' + Number(v.amount).toLocaleString('id-ID') }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ v.minimum_order > 0 ? 'Rp ' + Number(v.minimum_order).toLocaleString('id-ID') : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ v.used_count }}<span v-if="v.maximum_usage"> / {{ v.maximum_usage }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div>{{ formatDate(v.start_date) }}</div>
                                <div class="text-gray-400">s.d {{ formatDate(v.end_date) }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="isExpired(v)
                                        ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                        : v.status === 'active'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300'"
                                >
                                    {{ isExpired(v) ? 'Kedaluwarsa' : (v.status === 'active' ? 'Aktif' : 'Nonaktif') }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <Link :href="route('admin.vouchers.edit', v.id)" class="mr-2 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">Edit</Link>
                                <button @click="destroy(v.id)" class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="vouchers.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada voucher.</td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="vouchers.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Menampilkan {{ vouchers.from }}–{{ vouchers.to }} dari {{ vouchers.total }}</p>
                    <div class="flex gap-2">
                        <Link v-for="link in vouchers.links" :key="link.label" :href="link.url || '#'" class="rounded-lg px-3 py-1 text-sm" :class="link.active ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'" v-html="link.label" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>

    <ConfirmModal
        :show="showConfirmModal"
        title="Hapus Voucher"
        message="Yakin ingin menghapus voucher ini? Tindakan ini tidak dapat dibatalkan."
        :processing="deleting"
        @confirm="confirmDelete"
        @close="showConfirmModal = false"
    />
</template>
