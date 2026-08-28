<script setup>
import { ref } from 'vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Konfirmasi' },
    message: { type: String, default: 'Yakin ingin melanjutkan?' },
    confirmText: { type: String, default: 'Ya, Hapus' },
    cancelText: { type: String, default: 'Batal' },
    processing: { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'cancel', 'close'])

function onConfirm() {
    emit('confirm')
}

function onCancel() {
    emit('cancel')
    emit('close')
}
</script>

<template>
    <Modal :show="show" max-width="sm" @close="onCancel">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/50">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ message }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="onCancel"
                    :disabled="processing"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    {{ cancelText }}
                </button>
                <button
                    @click="onConfirm"
                    :disabled="processing"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 disabled:opacity-50"
                >
                    {{ processing ? 'Menghapus...' : confirmText }}
                </button>
            </div>
        </div>
    </Modal>
</template>