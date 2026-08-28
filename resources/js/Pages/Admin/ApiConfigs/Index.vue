<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    activeProvider: { type: String, default: 'tripay' },
    providers: { type: Object, required: true },
})

const activeTab = ref(props.activeProvider)

const tabConfig = {
    tripay: {
        label: 'Payment Gateway',
        fields: [
            { key: 'api_key', label: 'API Key', type: 'text', required: true },
            { key: 'private_key', label: 'Private Key', type: 'text', required: true },
            { key: 'merchant_code', label: 'Merchant Code', type: 'text', required: true },
            { key: 'is_production', label: 'Mode', type: 'select', required: true, options: { '0': 'Sandbox (Development)', '1': 'Production' } },
        ],
    },
    game_api: {
        label: 'API Games',
        fields: [
            { key: 'base_url', label: 'Base URL', type: 'text', required: true },
            { key: 'api_key', label: 'API Key', type: 'text', required: true },
            { key: 'secret_key', label: 'Secret Key', type: 'text', required: false },
            { key: 'merchant_id', label: 'Merchant ID', type: 'text', required: false },
        ],
    },
}

const currentConfig = computed(() => {
    const configs = props.providers[activeTab.value] ?? {}
    const fields = tabConfig[activeTab.value]?.fields ?? []

    const data = {}
    for (const field of fields) {
        data[field.key] = configs[field.key] ?? ''
    }

    return data
})

const form = useForm({
    provider: activeTab.value,
    configs: currentConfig.value,
})

function switchTab(provider) {
    activeTab.value = provider
    form.provider = provider
    form.configs = currentConfig.value
    form.clearErrors()
}

function submit() {
    form.put(route('admin.api-configs.update'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-3xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pengaturan API</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Konfigurasi kredensial untuk layanan eksternal.</p>

            <div class="mt-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-6">
                    <button
                        v-for="(config, provider) in tabConfig"
                        :key="provider"
                        @click="switchTab(provider)"
                        class="whitespace-nowrap border-b-2 pb-3 text-sm font-medium transition-colors"
                        :class="
                            activeTab === provider
                                ? 'border-primary-600 text-primary-700 dark:border-primary-400 dark:text-primary-400'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'
                        "
                    >
                        {{ config.label }}
                    </button>
                </nav>
            </div>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ activeTab === 'tripay' ? 'Masukkan kredensial dari dashboard Tripay.' : 'Masukkan kredensial API provider game.' }}
                    </p>

                    <div class="mt-4 space-y-4">
                        <div v-for="field in tabConfig[activeTab]?.fields" :key="field.key">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ field.label }}
                                <span v-if="field.required" class="text-red-500"> *</span>
                            </label>

                            <select
                                v-if="field.type === 'select'"
                                v-model="form.configs[field.key]"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option v-for="(label, val) in field.options" :key="val" :value="val">{{ label }}</option>
                            </select>

                            <input
                                v-else
                                v-model="form.configs[field.key]"
                                :type="field.type"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors['configs.' + field.key] }"
                            />
                            <p v-if="form.errors['configs.' + field.key]" class="mt-1 text-sm text-red-600">{{ form.errors['configs.' + field.key] }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
