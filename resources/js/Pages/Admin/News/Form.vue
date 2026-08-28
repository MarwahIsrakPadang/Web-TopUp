<script setup>
import { useForm } from '@inertiajs/vue3'
import { useDirtyWarning } from '@/Composables/useDirtyWarning'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    news: { type: Object, default: null },
})

const isEdit = !!props.news

const form = useForm({
    title: props.news?.title ?? '',
    slug: props.news?.slug ?? '',
    content: props.news?.content ?? '',
    thumbnail: null,
    status: props.news?.status ?? 'draft',
    published_at: props.news?.published_at ?? '',
})

useDirtyWarning(form)

function submit() {
    if (isEdit) {
        form.put(route('admin.news.update', props.news.id), {
            forceFormData: true,
            preserveScroll: true,
        })
    } else {
        form.post(route('admin.news.store'), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-3xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ isEdit ? 'Edit Berita' : 'Tambah Berita' }}
            </h1>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.slug }"
                                />
                                <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konten <span class="text-red-500">*</span></label>
                            <textarea
                                v-model="form.content"
                                rows="12"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-mono text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors.content }"
                            ></textarea>
                            <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                                <select
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="draft">Draf</option>
                                    <option value="published">Publikasikan</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jika dipublikasikan, tanggal publikasi otomatis diisi.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gambar Sampul</label>
                                <input
                                    type="file"
                                    accept="image/png,image/jpg,image/jpeg,image/webp"
                                    @input="form.thumbnail = $event.target.files[0]"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100 dark:text-gray-400 dark:file:bg-primary-900/50 dark:file:text-primary-300"
                                />
                                <p v-if="form.errors.thumbnail" class="mt-1 text-sm text-red-600">{{ form.errors.thumbnail }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a :href="route('admin.news.index')" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Batal</a>
                    <button
                        type="submit"
                        class="rounded-lg bg-primary-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Berita') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
