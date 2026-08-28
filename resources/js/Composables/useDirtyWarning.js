import { onMounted, onUnmounted } from 'vue'

export function useDirtyWarning(form) {
    function handleBeforeUnload(e) {
        if (form.isDirty) {
            e.preventDefault()
            e.returnValue = ''
        }
    }

    onMounted(() => window.addEventListener('beforeunload', handleBeforeUnload))
    onUnmounted(() => window.removeEventListener('beforeunload', handleBeforeUnload))

    return { isDirty: form.isDirty }
}