import { router } from '@inertiajs/vue3'

export function usePrefetch() {
    const prefetched = new Set()

    function prefetch(url) {
        if (prefetched.has(url)) return
        prefetched.add(url)
        router.prefetch(url, {}, { cacheFor: 60_000 })
    }

    return { prefetch }
}
