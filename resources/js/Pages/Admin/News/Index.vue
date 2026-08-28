<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
    newsList: { type: Object, required: true },
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
    router.delete(route('admin.news.destroy', itemToDelete.value), {
        onFinish: () => {
            showConfirmModal.value = false
            deleting.value = false
            itemToDelete.value = null
        },
    })
}

function formatDate(date) {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', { dateStyle: 'medium' })
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-7xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Berita</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Kelola berita dan pengumuman.</p>
                </div>
                <Link
                    :href="route('admin.news.create')"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500"
                >
                    + Tambah Berita
                </Link>
            </div>

            <div class="mt-6 space-y-4">
                <div v-for="item in newsList.data" :key="item.id" class="flex items-start gap-4 rounded-xl bg-white p-4 shadow-sm dark:bg-gray-800">
                    <div v-if="item.thumbnail" class="h-20 w-32 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                        <img :src="'/storage/' + item.thumbnail" :alt="item.title" loading="lazy" class="h-full w-full object-cover" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ item.title }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">{{ item.content?.substring(0, 150) }}...</p>
                        <div class="mt-2 flex items-center gap-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="item.status === 'published'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400'"
                            >
                                {{ item.status === 'published' ? 'Dipublikasikan' : 'Draf' }}
                            </span>
                            <span v-if="item.published_at" class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(item.published_at) }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <Link :href="route('admin.news.edit', item.id)" class="rounded-lg px-3 py-1.5 text-sm font-medium text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/50">Edit</Link>
                        <button @click="destroy(item.id)" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/50">Hapus</button>
                    </div>
                </div>

                <div v-if="newsList.data.length === 0" class="rounded-xl bg-white py-12 text-center shadow-sm dark:bg-gray-800">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada berita.</p>
                </div>
            </div>

            <div v-if="newsList.last_page > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">Menampilkan {{ newsList.from }}–{{ newsList.to }} dari {{ newsList.total }}</p>
                <div class="flex gap-2">
                    <Link v-for="link in newsList.links" :key="link.label" :href="link.url || '#'" class="rounded-lg px-3 py-1 text-sm" :class="link.active ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'" v-html="link.label" />
                </div>
            </div>
        </div>
    </AdminLayout>

    <ConfirmModal
        :show="showConfirmModal"
        title="Hapus Berita"
        message="Yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan."
        :processing="deleting"
        @confirm="confirmDelete"
        @close="showConfirmModal = false"
    />
</template>
