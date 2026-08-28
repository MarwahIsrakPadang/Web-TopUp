<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
    products: { type: Object, required: true },
    games: { type: Array, required: true },
})

const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '')
const showConfirmModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

function destroy(productId) {
    itemToDelete.value = productId
    showConfirmModal.value = true
}

function confirmDelete() {
    deleting.value = true
    router.delete(route('admin.products.destroy', itemToDelete.value), {
        onFinish: () => {
            showConfirmModal.value = false
            deleting.value = false
            itemToDelete.value = null
        },
    })
}

function filter() {
    router.get(route('admin.products.index'), { 
        game_id: new URLSearchParams(window.location.search).get('game_id') || '',
        search: searchQuery.value 
    }, { preserveState: true })
}

function filterByGame(gameId) {
    router.get(route('admin.products.index'), { 
        game_id: gameId || '',
        search: new URLSearchParams(window.location.search).get('search') || '' 
    }, { preserveState: true })
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6" style="max-width: 1280px;">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold" style="color: #E0E6ED;">Kelola Produk</h1>
                    <p class="mt-1 text-sm" style="color: #8899A6;">Atur daftar harga dan stok produk top-up.</p>
                </div>
                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition-all"
                    style="background-color: #A78BFA;"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Produk
                </Link>
            </div>

            <div class="flex items-center gap-4">
                <select
                    @change="filterByGame($event.target.value)"
                    class="rounded-lg border px-3 py-2 text-sm outline-none"
                    style="background-color: #0A0C10; border-color: #222732; color: #E0E6ED;"
                >
                    <option value="">Semua Game</option>
                    <option v-for="game in games" :key="game.id" :value="game.id">{{ game.name }}</option>
                </select>

                <div class="flex items-center gap-2">
                    <input 
                        v-model="searchQuery"
                        @keyup.enter.stop="filter"
                        type="text" 
                        placeholder="Cari produk..."
                        class="rounded-lg border px-3 py-2 text-sm outline-none"
                        style="background-color: #0A0C10; border-color: #222732; color: #E0E6ED;"
                    >
                    <button 
                        type="button"
                        @click.stop="filter" 
                        class="rounded-lg px-3 py-2 text-sm font-semibold text-white transition-all" 
                        style="background-color: #A78BFA;"
                    >
                        Cari
                    </button>
                </div>
            </div>

            <div class="rounded-xl border" style="background-color: #14171E; border-color: #222732;">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid #222732;">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Game</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="divide-color: rgba(34,39,50,0.5);">
                            <tr v-for="product in products.data" :key="product.id" class="transition-colors hover:bg-white/[0.02]">
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-medium" style="color: #E0E6ED;">{{ product.name }}</p>
                                    <p class="text-xs" style="color: #8899A6;">{{ product.slug }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-sm" style="color: #8899A6;">{{ product.game?.name }}</td>
                                <td class="px-4 py-3.5 text-sm" style="color: #8899A6;">{{ product.category?.name ?? '-' }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-medium" style="color: #A78BFA;">Rp {{ formatPrice(product.price) }}</span>
                                    <span v-if="product.original_price" class="ml-2 text-xs line-through" style="color: #8899A6;">Rp {{ formatPrice(product.original_price) }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :style="product.status === 'active'
                                            ? { backgroundColor: 'rgba(0,200,151,0.1)', color: '#00C897' }
                                            : { backgroundColor: 'rgba(248,113,113,0.1)', color: '#F87171' }"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full"
                                            :style="{ backgroundColor: product.status === 'active' ? '#00C897' : '#F87171' }"
                                        />
                                        {{ product.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('admin.products.edit', product.id)" class="rounded-lg px-2 py-1 text-xs font-medium transition-all hover:bg-white/5" style="color: #8899A6;">Edit</Link>
                                        <button @click="destroy(product.id)" class="rounded-lg px-2 py-1 text-xs font-medium transition-all hover:bg-white/5" style="color: #F87171;">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-sm" style="color: #8899A6;">
                                    Belum ada produk.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="products.last_page > 1" class="flex items-center justify-between border-t px-5 py-3" style="border-color: #222732;">
                    <p class="text-sm" style="color: #8899A6;">
                        Menampilkan {{ products.from }}–{{ products.to }} dari {{ products.total }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in products.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium transition-all"
                            :class="link.active ? 'text-white' : 'hover:bg-white/5'"
                            :style="link.active ? { backgroundColor: '#A78BFA', color: '#fff' } : { color: '#8899A6' }"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>

    <ConfirmModal
        :show="showConfirmModal"
        title="Hapus Produk"
        message="Yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan."
        :processing="deleting"
        @confirm="confirmDelete"
        @close="showConfirmModal = false"
    />
</template>
