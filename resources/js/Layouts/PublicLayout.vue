<script setup>
import { Link, Head, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Toast from '@/Components/Toast.vue'
import PillNav from '@/Components/PillNav.vue'
import { usePrefetch } from '@/Composables/usePrefetch.js'

const mobileOpen = ref(false)
const searchQuery = ref('')
const page = usePage()
const { prefetch } = usePrefetch()

const currentUrl = computed(() => page.url)

const navItems = [
  { label: 'Beranda', href: route('home') },
  { label: 'Game', href: '#games' },
  { label: 'Riwayat Transaksi', href: route('orders.index') },
]

function handleSearch() {
  if (searchQuery.value.trim()) {
    if (window.location.pathname === '/') {
      document.getElementById('games')?.scrollIntoView({ behavior: 'smooth' })
    } else {
      router.visit('/')
    }
  }
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-surface">
    <Toast />
    <header class="sticky top-0 z-50 border-b border-white/5 bg-surface/80 backdrop-blur-xl">
      <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 sm:gap-6">
          <Link :href="route('home')">
            <ApplicationLogo class="h-8 w-auto" />
          </Link>
          <PillNav
            :items="navItems"
            :activeHref="currentUrl"
            baseColor="#0F172A"
            pillColor="#6366F1"
            hoveredPillTextColor="#FFFFFF"
            pillTextColor="#0F172A"
            :initialLoadAnimation="false"
          />
        </div>
        <div class="hidden items-center gap-3 sm:flex">
          <div class="relative">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Cari game..."
              class="w-48 rounded-lg bg-surface-card px-3 py-1.5 text-sm text-white placeholder-text-muted outline-none ring-1 ring-white/10 transition-all focus:w-56 focus:ring-primary-500/50"
            />
            <svg class="absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
          </div>
          <Link :href="route('orders.tracking')" @mouseenter="prefetch(route('orders.tracking'))" class="rounded-lg bg-primary-600 px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:bg-primary-500">Riwayat Transaksi</Link>
        </div>
      </div>
    </header>

    <main class="flex-1">
      <slot />
    </main>

    <footer class="border-t border-white/5 bg-surface">
      <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
          <div>
            <div class="flex items-center gap-2">
              <ApplicationLogo class="h-8 w-auto" />
            </div>
            <p class="mt-3 text-sm leading-relaxed text-text-muted">Platform top-up game premium Indonesia. Proses instan, aman, dan terpercaya dengan metode pembayaran terlengkap.</p>
            <div class="mt-4 flex gap-3">
              <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-text-muted transition-colors hover:bg-primary-500/20 hover:text-primary-400">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
              </a>
              <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-text-muted transition-colors hover:bg-primary-500/20 hover:text-primary-400">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7.061 2.25H16.939a4.75 4.75 0 0 1 4.75 4.75V16.939a4.75 4.75 0 0 1-4.75 4.75H7.061a4.75 4.75 0 0 1-4.75-4.75V7.061a4.75 4.75 0 0 1 4.75-4.75Zm6.189 9.75a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0ZM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm4.5-2.25a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z" /></svg>
              </a>
              <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-text-muted transition-colors hover:bg-primary-500/20 hover:text-primary-400">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.82 4.26a10.14 10.14 0 0 0-.53 1.1 14.66 14.66 0 0 0-4.58 0 10.14 10.14 0 0 0-.53-1.1 16 16 0 0 0-4.13 1.3 17.33 17.33 0 0 0-3 11.59 16.6 16.6 0 0 0 5.07 2.59A12.89 12.89 0 0 0 8.23 18a9.65 9.65 0 0 1-1.71-.83 3.39 3.39 0 0 0 .42-.33 11.66 11.66 0 0 0 10.12 0q.21.18.42.33a10.84 10.84 0 0 1-1.71.83 12.89 12.89 0 0 0 1.08 1.12 16.6 16.6 0 0 0 5.07-2.59 17.22 17.22 0 0 0-3-11.59 16 16 0 0 0-4.13-1.3ZM8.74 12.8a1.41 1.41 0 1 1 0-2.81 1.41 1.41 0 0 1 0 2.81Zm6.52 0a1.41 1.41 0 1 1 0-2.81 1.41 1.41 0 0 1 0 2.81Z" /></svg>
              </a>
            </div>
          </div>

          <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Menu Cepat</h3>
            <ul class="mt-4 space-y-2.5">
              <li><a href="#" class="text-sm text-text-muted transition-colors hover:text-white">Kebijakan Privasi</a></li>
              <li><a href="#" class="text-sm text-text-muted transition-colors hover:text-white">Syarat & Ketentuan</a></li>
              <li><a href="#" class="text-sm text-text-muted transition-colors hover:text-white">Pusat Bantuan</a></li>
              <li><a href="#" class="text-sm text-text-muted transition-colors hover:text-white">Cara Order</a></li>
            </ul>
          </div>

          <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Game Populer</h3>
            <ul class="mt-4 space-y-2.5">
              <li><Link :href="route('games.show', { slug: 'mobile-legends' })" class="text-sm text-text-muted transition-colors hover:text-white">Mobile Legends</Link></li>
              <li><Link :href="route('games.show', { slug: 'free-fire' })" class="text-sm text-text-muted transition-colors hover:text-white">Free Fire</Link></li>
              <li><Link :href="route('games.show', { slug: 'valorant' })" class="text-sm text-text-muted transition-colors hover:text-white">Valorant</Link></li>
              <li><Link :href="route('games.show', { slug: 'genshin-impact' })" class="text-sm text-text-muted transition-colors hover:text-white">Genshin Impact</Link></li>
            </ul>
          </div>

          <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Hubungi Kami</h3>
            <p class="mt-4 text-sm leading-relaxed text-text-muted">Tim support kami siap membantu Anda 24/7. Jangan ragu untuk menghubungi kami kapan saja.</p>
            <a href="#" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-500">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
              Contact Us
            </a>
          </div>
        </div>
      </div>

      <div class="border-t border-white/5 py-6" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom));">
        <p class="text-center text-sm text-text-muted">&copy; {{ new Date().getFullYear() }} TopUpGame. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>
