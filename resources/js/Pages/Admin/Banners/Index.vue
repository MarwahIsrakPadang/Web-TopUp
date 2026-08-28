<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
    banners: { type: Array, required: true },
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
    router.delete(route('admin.banners.destroy', itemToDelete.value), {
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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Banner</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Kelola banner promosi di halaman utama.</p>
                </div>
                <Link
                    :href="route('admin.banners.create')"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500"
                >
                    + Tambah Banner
                </Link>
            </div>

            <div class="mt-6 space-y-4">
                <div v-for="banner in banners" :key="banner.id" class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm dark:bg-gray-800">
                    <div class="h-24 w-48 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                        <img :src="'/storage/' + banner.image" :alt="banner.title" loading="lazy" class="h-full w-full object-cover" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ banner.title }}</h3>
                        <p v-if="banner.subtitle" class="text-sm text-gray-500 dark:text-gray-400">{{ banner.subtitle }}</p>
                        <p v-if="banner.link" class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate">{{ banner.link }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="banner.status === 'active'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300'"
                            >
                                {{ banner.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Urutan: {{ banner.sort_order }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <Link :href="route('admin.banners.edit', banner.id)" class="rounded-lg px-3 py-1.5 text-sm font-medium text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/50">Edit</Link>
                        <button @click="destroy(banner.id)" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/50">Hapus</button>
                    </div>
                </div>

                <div v-if="banners.length === 0" class="rounded-xl bg-white py-12 text-center shadow-sm dark:bg-gray-800">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada banner.</p>
                </div>
            </div>
        </div>
    </AdminLayout>

    <ConfirmModal
        :show="showConfirmModal"
        title="Hapus Banner"
        message="Yakin ingin menghapus banner ini? Tindakan ini tidak dapat dibatalkan."
        :processing="deleting"
        @confirm="confirmDelete"
        @close="showConfirmModal = false"
    />
</template>
