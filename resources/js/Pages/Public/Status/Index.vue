<script setup>
import { useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const form = useForm({
    invoice_number: '',
})

function submit() {
    form.post(route('status.check.post'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <PublicLayout>
        <div class="mx-auto max-w-lg px-4 py-16 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-8 shadow-sm dark:bg-gray-800">
                <div class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/50">
                        <svg class="h-7 w-7 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.181-3.182" />
                        </svg>
                    </div>
                    <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100">Cek Status Pesanan</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Masukkan nomor invoice untuk melihat status pesanan.</p>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Invoice <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.invoice_number"
                            type="text"
                            placeholder="Contoh: INV/20260719/12345"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            :class="{ 'border-red-500': form.errors.invoice_number }"
                        />
                        <p v-if="form.errors.invoice_number" class="mt-1 text-sm text-red-600">{{ form.errors.invoice_number }}</p>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Mengecek...' : 'Cek Status' }}
                    </button>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
