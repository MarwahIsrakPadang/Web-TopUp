<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
    methods: { type: Array, required: true },
})

const showConfirmModal = ref(false)
const itemToDelete = ref(null)
const deleteType = ref('')
const deleting = ref(false)

function destroyMethod(id) {
    itemToDelete.value = id
    deleteType.value = 'method'
    showConfirmModal.value = true
}

function destroyChannel(id) {
    itemToDelete.value = id
    deleteType.value = 'channel'
    showConfirmModal.value = true
}

function confirmDelete() {
    deleting.value = true
    const routeName = deleteType.value === 'method'
        ? route('admin.payment-methods.destroy', itemToDelete.value)
        : route('admin.channels.destroy', itemToDelete.value)
    router.delete(routeName, {
        onFinish: () => {
            showConfirmModal.value = false
            deleting.value = false
            itemToDelete.value = null
        },
    })
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-5xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pembayaran</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Kelola metode dan channel pembayaran.</p>
                </div>
                <Link
                    :href="route('admin.payment-methods.create')"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500"
                >
                    + Tambah Metode
                </Link>
            </div>

            <div class="mt-6 space-y-6">
                <div v-for="method in methods" :key="method.id" class="rounded-xl bg-white shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div v-if="method.icon" class="h-8 w-8 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                                <img :src="'/storage/' + method.icon" :alt="method.name" loading="lazy" class="h-full w-full object-contain p-1" />
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ method.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ method.code }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="method.status === 'active'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300'"
                            >
                                {{ method.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <Link :href="route('admin.payment-methods.edit', method.id)" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">Edit</Link>
                            <button @click="destroyMethod(method.id)" class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">Hapus</button>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Channel</h4>
                            <Link
                                :href="route('admin.payment-methods.channels.create', method.id)"
                                class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                            >
                                + Tambah Channel
                            </Link>
                        </div>

                        <div v-if="method.channels.length === 0" class="rounded-lg border-2 border-dashed border-gray-200 p-6 text-center dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada channel.</p>
                        </div>

                        <div v-else class="space-y-2">
                            <div v-for="channel in method.channels" :key="channel.id" class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div v-if="channel.icon" class="h-6 w-6 overflow-hidden rounded">
                                        <img :src="'/storage/' + channel.icon" :alt="channel.name" loading="lazy" class="h-full w-full object-contain" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ channel.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ channel.code }}
                                            <span v-if="channel.fee_amount > 0"> — Biaya: {{ channel.fee_type === 'percentage' ? channel.fee_amount + '%' : 'Rp ' + channel.fee_amount.toLocaleString('id-ID') }}</span>
                                            <span v-if="channel.minimum_amount"> — Min: Rp {{ channel.minimum_amount.toLocaleString('id-ID') }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="channel.status === 'active'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300'"
                                    >
                                        {{ channel.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <Link :href="route('admin.channels.edit', channel.id)" class="text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">Edit</Link>
                                    <button @click="destroyChannel(channel.id)" class="text-xs font-medium text-red-600 hover:text-red-500 dark:text-red-400">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="methods.length === 0" class="rounded-xl bg-white py-12 text-center shadow-sm dark:bg-gray-800">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada metode pembayaran.</p>
                </div>
            </div>
        </div>
    </AdminLayout>

    <ConfirmModal
        :show="showConfirmModal"
        :title="deleteType === 'method' ? 'Hapus Metode Pembayaran' : 'Hapus Channel'"
        :message="deleteType === 'method' ? 'Yakin ingin menghapus metode pembayaran ini? Tindakan ini tidak dapat dibatalkan.' : 'Yakin ingin menghapus channel ini? Tindakan ini tidak dapat dibatalkan.'"
        :processing="deleting"
        @confirm="confirmDelete"
        @close="showConfirmModal = false"
    />
</template>
