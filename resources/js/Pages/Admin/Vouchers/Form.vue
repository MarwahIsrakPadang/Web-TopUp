<script setup>
import { useForm } from '@inertiajs/vue3'
import { useDirtyWarning } from '@/Composables/useDirtyWarning'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    voucher: { type: Object, default: null },
})

const isEdit = !!props.voucher

function formatForInput(date) {
    if (!date) return ''
    const d = new Date(date)
    const pad = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const form = useForm({
    code: props.voucher?.code ?? '',
    type: props.voucher?.type ?? 'percentage',
    amount: props.voucher?.amount ?? '',
    minimum_order: props.voucher?.minimum_order ?? 0,
    maximum_usage: props.voucher?.maximum_usage ?? '',
    start_date: formatForInput(props.voucher?.start_date) ?? '',
    end_date: formatForInput(props.voucher?.end_date) ?? '',
    status: props.voucher?.status ?? 'active',
})

useDirtyWarning(form)

function submit() {
    if (isEdit) {
        form.put(route('admin.vouchers.update', props.voucher.id), {
            forceFormData: true,
            preserveScroll: true,
        })
    } else {
        form.post(route('admin.vouchers.store'), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ isEdit ? 'Edit Voucher' : 'Tambah Voucher' }}
            </h1>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Voucher <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.code"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-mono text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.code }"
                                />
                                <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Diskon <span class="text-red-500">*</span></label>
                                <select
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="nominal">Nominal (Rp)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ form.type === 'percentage' ? 'Diskon (%)' : 'Diskon (Rp)' }}
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    min="0"
                                    :max="form.type === 'percentage' ? 100 : null"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.amount }"
                                />
                                <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimal Belanja (Rp)</label>
                                <input
                                    v-model="form.minimum_order"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maksimal Penggunaan</label>
                            <input
                                v-model="form.maximum_usage"
                                type="number"
                                min="1"
                                placeholder="Kosongkan jika tidak terbatas"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ada batas pemakaian.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.start_date"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.start_date }"
                                />
                                <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Berakhir <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.end_date"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.end_date }"
                                />
                                <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">{{ form.errors.end_date }}</p>
                            </div>
                        </div>

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
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a :href="route('admin.vouchers.index')" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Batal</a>
                    <button
                        type="submit"
                        class="rounded-lg bg-primary-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Voucher') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
