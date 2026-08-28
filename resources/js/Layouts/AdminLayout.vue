<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Toast from '@/Components/Toast.vue'
import Icon from '@/Components/Icon.vue'

const page = usePage()
const sidebarOpen = ref(false)
const invoiceSearchQuery = ref('')

const isDashboard = computed(() => {
    return window.location.pathname === '/admin/dashboard'
})

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: 'home', match: 'admin.dashboard' },
    { name: 'Game', href: route('admin.games.index'), icon: 'gamepad', match: 'admin.games.*' },
    { name: 'Produk', href: route('admin.products.index'), icon: 'shoppingCart', match: 'admin.products.*' },
    { name: 'Pembayaran', href: route('admin.payment-methods.index'), icon: 'creditCard', match: 'admin.payment-methods.*' },
    { name: 'Banner', href: route('admin.banners.index'), icon: 'trendingUp', match: 'admin.banners.*' },
    { name: 'Voucher', href: route('admin.vouchers.index'), icon: 'wallet', match: 'admin.vouchers.*' },
    { name: 'Berita', href: route('admin.news.index'), icon: 'activity', match: 'admin.news.*' },
    { name: 'Laporan', href: route('admin.reports.index'), icon: 'barChart', match: 'admin.reports.*' },
    { name: 'API Config', href: route('admin.api-configs.index'), icon: 'settings', match: 'admin.api-configs.*' },
    { name: 'Pengaturan', href: route('admin.settings.index'), icon: 'building', match: 'admin.settings.*' },
]

function isActive(item) {
    return route().current(item.match)
}

function closeSidebar() {
    sidebarOpen.value = false
}

function submitLogout() {
    router.post(route('logout'))
}

function searchInvoice() {
    const query = invoiceSearchQuery.value.trim()
    if (!query) return
    router.visit(route('invoice.show', query))
}
</script>

<template>
    <div class="flex h-screen" style="background-color:#0A0C10;color:#E0E6ED;">
        <Toast />

        <!-- Overlay mobile -->
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-200"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden"
                @click="closeSidebar"
            />
        </transition>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r transition-transform duration-300 lg:static lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }"
            style="background-color:#111419;border-color:#222732;"
        >
            <div class="flex h-16 items-center justify-between gap-3 border-b px-5" style="border-color:#222732;">
                <Link href="/admin/dashboard" class="flex items-center gap-3" @click="closeSidebar">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:linear-gradient(135deg,#A78BFA,#8B5CF6);">
                        <span class="text-sm font-bold text-white">TG</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold" style="color:#E0E6ED;">TopUpGame</p>
                        <p class="text-[10px] font-medium uppercase tracking-wider" style="color:#8899A6;">Admin Console</p>
                    </div>
                </Link>
                <button
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-[#8899A6] transition-all hover:bg-white/5 hover:text-[#E0E6ED] lg:hidden"
                    @click="closeSidebar"
                >
                    <Icon name="x" :size="18" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-widest" style="color:#8899A6;">
                    Menu Utama
                </p>
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="group relative mt-0.5 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all"
                    :class="isActive(item)
                        ? 'bg-[#A78BFA]/10 text-[#A78BFA]'
                        : 'text-[#8899A6] hover:bg-white/5 hover:text-[#E0E6ED]'"
                    @click="closeSidebar"
                >
                    <span
                        v-if="isActive(item)"
                        class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full"
                        style="background:linear-gradient(180deg,#A78BFA,#8B5CF6);box-shadow:0 0 10px rgba(167,139,250,0.6);"
                    />
                    <Icon :name="item.icon" :size="18" />
                    {{ item.name }}
                </Link>
            </nav>

            <div class="border-t p-3" style="border-color:#222732;">
                <button
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-[#8899A6] transition-all hover:bg-[#F87171]/10 hover:text-[#F87171]"
                    @click="submitLogout"
                >
                    <Icon name="arrowLeft" :size="18" />
                    Keluar
                </button>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="flex h-16 shrink-0 items-center justify-between gap-4 border-b px-4 sm:px-6" style="background-color:#14171E;border-color:#222732;">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <button
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border text-[#8899A6] transition-all hover:text-[#E0E6ED] lg:hidden"
                        style="background-color:#0A0C10;border-color:#222732;"
                        @click="sidebarOpen = true"
                    >
                        <Icon name="menu" :size="18" />
                    </button>
                    <!-- Pengecekan route yang lebih akurat -->
                    <div v-if="isDashboard" class="relative w-full max-w-md">
                        <Icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" style="color:#8899A6;" />
                        <input
                            v-model="invoiceSearchQuery"
                            type="text"
                            placeholder="Cari invoice... (Enter)"
                            class="w-full rounded-lg border py-2 pl-10 pr-4 text-sm outline-none transition-all focus:border-[#A78BFA]/50 focus:ring-1 focus:ring-[#A78BFA]/20"
                            style="background-color:#0A0C10;border-color:#222732;color:#E0E6ED;"
                            @keydown.enter.stop="searchInvoice"
                        />
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <button
                        class="hidden h-9 items-center gap-2 rounded-lg border px-3 text-xs font-medium text-[#8899A6] transition-all hover:border-[#A78BFA]/40 hover:text-[#E0E6ED] sm:flex"
                        style="background-color:#0A0C10;border-color:#222732;"
                        @click="submitLogout"
                    >
                        <Icon name="arrowLeft" :size="14" />
                        Keluar
                    </button>
                    <div class="h-6 w-px" style="background-color:#222732;"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-medium" style="color:#E0E6ED;">{{ page.props.auth?.user?.name || 'Admin' }}</p>
                            <p class="text-[10px]" style="color:#8899A6;">#ADM-{{ page.props.auth?.user?.id || '0000' }}</p>
                        </div>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br text-xs font-bold ring-2" style="background:linear-gradient(135deg,rgba(167,139,250,0.3),rgba(139,92,246,0.3));color:#A78BFA;--tw-ring-color:rgba(167,139,250,0.3);">
                            {{ (page.props.auth?.user?.name || 'A').charAt(0).toUpperCase() }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6" style="background-color:#0A0C10;">
                <slot />
            </main>
        </div>
    </div>
</template>
