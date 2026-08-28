<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const toasts = ref([])
let toastId = 0

watch(
    () => page.props.flash,
    (flash) => {
        if (flash.success) {
            addToast('success', flash.success)
        }
        if (flash.error) {
            addToast('error', flash.error)
        }
        if (flash.info) {
            addToast('info', flash.info)
        }
    },
    { deep: true }
)

function addToast(type, message) {
    const id = ++toastId
    toasts.value.push({ id, type, message })
    setTimeout(() => {
        removeToast(id)
    }, 5000)
}

function removeToast(id) {
    const idx = toasts.value.findIndex(t => t.id === id)
    if (idx !== -1) toasts.value.splice(idx, 1)
}

const typeStyles = {
    success: 'bg-green-50 border-green-400 text-green-800 dark:bg-green-900/50 dark:border-green-600 dark:text-green-300',
    error: 'bg-red-50 border-red-400 text-red-800 dark:bg-red-900/50 dark:border-red-600 dark:text-red-300',
    info: 'bg-blue-50 border-blue-400 text-blue-800 dark:bg-blue-900/50 dark:border-blue-600 dark:text-blue-300',
}

const typeIcons = {
    success: 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
    error: 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
    info: 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
}
</script>

<template>
    <div class="pointer-events-none fixed right-4 top-4 z-[9999] flex flex-col gap-3">
        <TransitionGroup name="toast">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex items-start gap-3 rounded-lg border-l-4 p-4 shadow-lg transition-all"
                :class="typeStyles[toast.type]"
            >
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="typeIcons[toast.type]" />
                </svg>
                <p class="text-sm font-medium">{{ toast.message }}</p>
                <button @click="removeToast(toast.id)" class="ml-auto shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active { transition: all 0.3s ease-out; }
.toast-leave-active { transition: all 0.2s ease-in; }
.toast-enter-from { opacity: 0; transform: translateX(100%); }
.toast-leave-to { opacity: 0; transform: translateX(100%); }
</style>