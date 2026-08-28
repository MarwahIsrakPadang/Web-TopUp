<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    game: { type: Object, required: true },
    product: { type: Object, required: true },
    paymentMethods: { type: Array, required: true },
})

const form = useForm({
    game_id: props.game.id,
    product_id: props.product.id,
    payment_channel_id: '',
    player_id: '',
    player_server: '',
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    note: '',
    voucher_code: '',
})

const selectedMethod = ref(null)
const voucherStatus = ref(null)

const channels = computed(() => {
    if (!selectedMethod.value) return []
    const method = props.paymentMethods.find(m => m.id === Number(selectedMethod.value))
    return method?.channels ?? []
})

const selectedChannel = computed(() => {
    if (!form.payment_channel_id) return null
    for (const method of props.paymentMethods) {
        const ch = method.channels.find(c => c.id === Number(form.payment_channel_id))
        if (ch) return ch
    }
    return null
})

const feeAmount = computed(() => {
    if (!selectedChannel.value) return 0
    if (selectedChannel.value.fee_type === 'percentage') {
        return Math.round(props.product.price * selectedChannel.value.fee_amount / 100)
    }
    return Number(selectedChannel.value.fee_amount)
})

const totalAmount = computed(() => {
    return props.product.price + feeAmount.value
})

function formatPrice(value) {
    return 'Rp ' + Number(value).toLocaleString('id-ID')
}

function validateVoucher() {
    if (!form.voucher_code) {
        voucherStatus.value = null
        return
    }
    router.post(route('voucher.validate'), {
        code: form.voucher_code,
        amount: props.product.price,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            voucherStatus.value = { valid: true, discount: page.props.discount }
        },
        onError: (errors) => {
            voucherStatus.value = { valid: false, message: errors.code || 'Voucher tidak valid' }
        },
    })
}

function submit() {
    form.post(route('checkout.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <PublicLayout>
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <h1 class="text-2xl font-bold text-white">Checkout</h1>

                    <form @submit.prevent="submit" class="mt-6 space-y-6">
                        <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                            <h2 class="font-semibold text-white">Data Player</h2>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300">ID Player <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.player_id"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-white outline-none transition-all placeholder:text-text-muted focus:border-primary-500/60 focus:ring-1 focus:ring-primary-500/40"
                                        :class="{ 'border-red-500': form.errors.player_id }"
                                    />
                                    <p v-if="form.errors.player_id" class="mt-1 text-sm text-red-600">{{ form.errors.player_id }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300">Server (opsional)</label>
                                    <input
                                        v-model="form.player_server"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-white outline-none transition-all placeholder:text-text-muted focus:border-primary-500/60 focus:ring-1 focus:ring-primary-500/40"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                            <h2 class="font-semibold text-white">Data Kontak (opsional)</h2>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300">Nama</label>
                                    <input v-model="form.customer_name" type="text" class="mt-1 block w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-white outline-none transition-all placeholder:text-text-muted focus:border-primary-500/60 focus:ring-1 focus:ring-primary-500/40" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300">Email</label>
                                    <input v-model="form.customer_email" type="email" class="mt-1 block w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-white outline-none transition-all placeholder:text-text-muted focus:border-primary-500/60 focus:ring-1 focus:ring-primary-500/40" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300">No. Telepon</label>
                                    <input v-model="form.customer_phone" type="text" class="mt-1 block w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-white outline-none transition-all placeholder:text-text-muted focus:border-primary-500/60 focus:ring-1 focus:ring-primary-500/40" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                            <h2 class="font-semibold text-white">Metode Pembayaran</h2>
                            <div class="mt-4 space-y-4">
                                <div v-for="method in paymentMethods" :key="method.id" class="rounded-lg border border-white/10">
                                    <button
                                        type="button"
                                        @click="selectedMethod = method.id; form.payment_channel_id = ''"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left"
                                    >
                                        <div v-if="method.icon" class="h-8 w-8 overflow-hidden rounded">
                                            <img :src="'/storage/' + method.icon" :alt="method.name" loading="lazy" class="h-full w-full object-contain" />
                                        </div>
                                        <span class="font-medium text-white">{{ method.name }}</span>
                                        <svg
                                            class="ml-auto h-5 w-5 text-text-muted transition-transform"
                                            :class="{ 'rotate-180': selectedMethod === method.id }"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>

                                    <div v-if="selectedMethod === method.id && method.channels.length > 0" class="border-t border-white/10 px-4 py-3">
                                        <div class="space-y-2">
                                            <label
                                                v-for="ch in method.channels"
                                                :key="ch.id"
                                                class="flex cursor-pointer items-center gap-3 rounded-lg p-3 transition-colors hover:bg-white/5"
                                                :class="{ 'bg-primary-500/10': form.payment_channel_id === ch.id }"
                                            >
                                                <input
                                                    type="radio"
                                                    :value="ch.id"
                                                    v-model="form.payment_channel_id"
                                                    class="h-4 w-4 accent-primary-500"
                                                />
                                                <div class="flex items-center gap-2">
                                                    <div v-if="ch.icon" class="h-6 w-6 overflow-hidden rounded">
                                                        <img :src="'/storage/' + ch.icon" :alt="ch.name" loading="lazy" class="h-full w-full object-contain" />
                                                    </div>
                                                    <span class="text-sm text-slate-200">{{ ch.name }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <p v-if="form.errors.payment_channel_id" class="text-sm text-red-600">{{ form.errors.payment_channel_id }}</p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                            <h2 class="font-semibold text-white">Voucher (opsional)</h2>
                            <div class="mt-4 flex gap-3">
                                <input
                                    v-model="form.voucher_code"
                                    type="text"
                                    placeholder="Masukkan kode voucher"
                                    class="block w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-white outline-none transition-all placeholder:text-text-muted focus:border-primary-500/60 focus:ring-1 focus:ring-primary-500/40"
                                />
                                <button
                                    type="button"
                                    @click="validateVoucher"
                                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500"
                                >
                                    Pakai
                                </button>
                            </div>
                            <p v-if="voucherStatus?.valid" class="mt-2 text-sm text-green-600">Diskon: {{ formatPrice(voucherStatus.discount) }}</p>
                            <p v-else-if="voucherStatus?.valid === false" class="mt-2 text-sm text-red-600">{{ voucherStatus.message }}</p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50 sm:w-auto"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Memproses...' : 'Buat Pesanan' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-24 rounded-xl bg-surface-card p-6 ring-1 ring-white/5">
                        <h2 class="font-semibold text-white">Ringkasan Pesanan</h2>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 overflow-hidden rounded-lg bg-white/5">
                                    <img v-if="game.icon" :src="'/storage/' + game.icon" :alt="game.name" loading="lazy" class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ game.name }}</p>
                                    <p class="text-xs text-text-muted">{{ product.name }}</p>
                                </div>
                            </div>

                            <div class="border-t border-white/10 pt-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-muted">Harga</span>
                                    <span class="font-medium text-white">{{ formatPrice(product.price) }}</span>
                                </div>
                                <div class="mt-2 flex justify-between text-sm">
                                    <span class="text-text-muted">Biaya</span>
                                    <span class="font-medium text-white">{{ formatPrice(feeAmount) }}</span>
                                </div>
                                <div class="mt-2 flex justify-between border-t border-white/10 pt-2">
                                    <span class="font-semibold text-white">Total</span>
                                    <span class="text-lg font-bold text-primary-400">{{ formatPrice(totalAmount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
