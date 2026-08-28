<script setup>
import { useForm } from '@inertiajs/vue3'
import { useDirtyWarning } from '@/Composables/useDirtyWarning'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    banner: { type: Object, default: null },
})

const isEdit = !!props.banner

const form = useForm({
    title: props.banner?.title ?? '',
    subtitle: props.banner?.subtitle ?? '',
    image: null,
    link: props.banner?.link ?? '',
    status: props.banner?.status ?? 'active',
    sort_order: props.banner?.sort_order ?? 0,
})

useDirtyWarning(form)

function submit() {
    if (isEdit) {
        form.put(route('admin.banners.update', props.banner.id), {
            forceFormData: true,
            preserveScroll: true,
        })
    } else {
        form.post(route('admin.banners.store'), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ isEdit ? 'Edit Banner' : 'Tambah Banner' }}
            </h1>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div v-if="isEdit && props.banner.image" class="mb-6 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                        <img :src="'/storage/' + props.banner.image" loading="lazy" class="h-48 w-full object-cover" />
                        <p class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">Gambar saat ini</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subjudul</label>
                            <input
                                v-model="form.subtitle"
                                type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Gambar <span v-if="!isEdit" class="text-red-500">*</span>
                                <span class="ml-2 text-xs font-normal text-gray-400">Rekomendasi: 1920x600px, maks 2MB</span>
                            </label>
                            <input
                                type="file"
                                accept="image/png,image/jpg,image/jpeg,image/webp"
                                @input="form.image = $event.target.files[0]"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100 dark:text-gray-400 dark:file:bg-primary-900/50 dark:file:text-primary-300"
                            />
                            <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tautan (opsional)</label>
                            <input
                                v-model="form.link"
                                type="text"
                                placeholder="https://..."
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL tujuan saat banner diklik.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                                <select
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urutan</label>
                                <input
                                    v-model="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a :href="route('admin.banners.index')" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Batal</a>
                    <button
                        type="submit"
                        class="rounded-lg bg-primary-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Banner') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
