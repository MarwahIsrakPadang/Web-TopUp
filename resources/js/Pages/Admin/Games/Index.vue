<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
    games: { type: Object, required: true },
})

const showConfirmModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

function destroy(gameId) {
    itemToDelete.value = gameId
    showConfirmModal.value = true
}

function confirmDelete() {
    deleting.value = true
    router.delete(route('admin.games.destroy', itemToDelete.value), {
        onFinish: () => {
            showConfirmModal.value = false
            deleting.value = false
            itemToDelete.value = null
        },
    })
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6" style="max-width: 1280px;">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold" style="color: #E0E6ED;">Manajemen Game</h1>
                    <p class="mt-1 text-sm" style="color: #8899A6;">Kelola daftar game yang tersedia di platform.</p>
                </div>
                <Link
                    :href="route('admin.games.create')"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition-all"
                    style="background-color: #A78BFA;"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Game
                </Link>
            </div>

            <div class="rounded-xl border" style="background-color: #14171E; border-color: #222732;">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid #222732;">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Game</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Urutan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: #8899A6;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="divide-color: rgba(34,39,50,0.5);">
                            <tr v-for="game in games.data" :key="game.id" class="transition-colors hover:bg-white/[0.02]">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div v-if="game.icon" class="h-10 w-10 shrink-0 overflow-hidden rounded-xl">
                                            <img :src="'/storage/' + game.icon" :alt="game.name" loading="lazy" class="h-full w-full object-cover" />
                                        </div>
                                        <div v-else class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#A78BFA]/30 to-purple-500/30 text-sm font-bold" style="color: #A78BFA;">
                                            {{ game.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium" style="color: #E0E6ED;">{{ game.name }}</p>
                                            <p class="text-xs" style="color: #8899A6;">{{ game.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :style="game.status === 'active'
                                            ? { backgroundColor: 'rgba(0,200,151,0.1)', color: '#00C897' }
                                            : { backgroundColor: 'rgba(248,113,113,0.1)', color: '#F87171' }"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full"
                                            :style="{ backgroundColor: game.status === 'active' ? '#00C897' : '#F87171' }"
                                        />
                                        {{ game.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-sm" style="color: #8899A6;">{{ game.sort_order }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('admin.games.edit', game.id)" class="rounded-lg px-2 py-1 text-xs font-medium transition-all hover:bg-white/5" style="color: #8899A6;">Edit</Link>
                                        <button @click="destroy(game.id)" class="rounded-lg px-2 py-1 text-xs font-medium transition-all hover:bg-white/5" style="color: #F87171;">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="games.data.length === 0">
                                <td colspan="4" class="px-4 py-12 text-center text-sm" style="color: #8899A6;">
                                    Belum ada game. Tambahkan game pertama Anda.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="games.last_page > 1" class="flex items-center justify-between border-t px-5 py-3" style="border-color: #222732;">
                    <p class="text-sm" style="color: #8899A6;">
                        Menampilkan {{ games.from }}–{{ games.to }} dari {{ games.total }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in games.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium transition-all"
                            :class="link.active
                                ? 'text-white'
                                : 'hover:bg-white/5'"
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
        title="Hapus Game"
        message="Yakin ingin menghapus game ini? Tindakan ini tidak dapat dibatalkan."
        :processing="deleting"
        @confirm="confirmDelete"
        @close="showConfirmModal = false"
    />
</template>
