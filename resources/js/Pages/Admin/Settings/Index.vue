<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    settings: { type: Object, required: true },
    formConfig: { type: Object, required: true },
})

const activeTab = computed(() => Object.keys(props.formConfig)[0])

const formData = computed(() => {
    const data = {}
    for (const [group, config] of Object.entries(props.formConfig)) {
        data[group] = {}
        for (const key of Object.keys(config.fields)) {
            data[group][key] = props.settings[group]?.find(s => s.key === key)?.value ?? ''
        }
    }
    return data
})

const form = useForm({ group: '', settings: {} })

function setActiveTab(group) {
    activeTab.value = group
}

function submit(group) {
    form.group = group
    form.settings = formData.value[group]
    form.put(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-3xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pengaturan Website</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Kelola pengaturan website Anda.</p>

            <div class="mt-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-6">
                    <button
                        v-for="(config, group) in formConfig"
                        :key="group"
                        @click="setActiveTab(group)"
                        class="whitespace-nowrap border-b-2 pb-3 text-sm font-medium transition-colors"
                        :class="
                            activeTab === group
                                ? 'border-primary-600 text-primary-700 dark:border-primary-400 dark:text-primary-400'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'
                        "
                    >
                        {{ config.label }}
                    </button>
                </nav>
            </div>

            <div class="mt-6">
                <form
                    v-for="(config, group) in formConfig"
                    :key="group"
                    v-show="activeTab === group"
                    @submit.prevent="submit(group)"
                    class="space-y-6"
                >
                    <div v-for="(field, key) in config.fields" :key="key">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ field.label }}
                        </label>

                        <input
                            v-if="field.type === 'text' || field.type === 'email' || field.type === 'number'"
                            v-model="formData[group][key]"
                            :type="field.type"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition-colors focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-primary-400"
                        />

                        <textarea
                            v-else-if="field.type === 'textarea'"
                            v-model="formData[group][key]"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition-colors focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-primary-400"
                        ></textarea>

                        <input
                            v-else-if="field.type === 'color'"
                            v-model="formData[group][key]"
                            type="color"
                            class="mt-1 h-10 w-20 cursor-pointer rounded border border-gray-300 bg-white p-1 dark:border-gray-600"
                        />

                        <select
                            v-else-if="field.type === 'select'"
                            v-model="formData[group][key]"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition-colors focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-primary-400"
                        >
                            <option v-for="(label, val) in field.options" :key="val" :value="val">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 dark:focus:ring-offset-gray-900"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
