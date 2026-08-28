<script setup>
import { useForm } from '@inertiajs/vue3'
import { useDirtyWarning } from '@/Composables/useDirtyWarning'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    channel: { type: Object, default: null },
    paymentMethod: { type: Object, required: true },
})

const isEdit = !!props.channel

const form = useForm({
    payment_method_id: props.paymentMethod.id,
    name: props.channel?.name ?? '',
    code: props.channel?.code ?? '',
    minimum_amount: props.channel?.minimum_amount ?? '',
    maximum_amount: props.channel?.maximum_amount ?? '',
    fee_type: props.channel?.fee_type ?? 'fixed',
    fee_amount: props.channel?.fee_amount ?? 0,
    status: props.channel?.status ?? 'active',
})

useDirtyWarning(form)

function submit() {
    if (isEdit) {
        form.put(route('admin.channels.update', props.channel.id), {
            forceFormData: true,
            preserveScroll: true,
        })
    } else {
        form.post(route('admin.payment-methods.channels.store', props.paymentMethod.id), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ isEdit ? 'Edit Channel' : 'Tambah Channel' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Metode: <span class="font-medium">{{ paymentMethod.name }}</span>
            </p>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Channel <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.code"
                                type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors.code }"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Contoh: bca_va, gopay, indomaret</p>
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimal Pembayaran</label>
                                <input
                                    v-model="form.minimum_amount"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maksimal Pembayaran</label>
                                <input
                                    v-model="form.maximum_amount"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Biaya <span class="text-red-500">*</span></label>
                                <select
                                    v-model="form.fee_type"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="fixed">Tetap (Rp)</option>
                                    <option value="percentage">Persentase (%)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.fee_amount"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.fee_amount }"
                                />
                                <p v-if="form.errors.fee_amount" class="mt-1 text-sm text-red-600">{{ form.errors.fee_amount }}</p>
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
                    <a :href="route('admin.payment-methods.index')" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Batal</a>
                    <button
                        type="submit"
                        class="rounded-lg bg-primary-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Channel') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
