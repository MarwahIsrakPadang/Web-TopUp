import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5174,
        strictPort: true,
        hmr: {
            host: '127.0.0.1',
            port: 5174,
        },
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: 'resources/js/app.js',
            refresh: {
                paths: ['resources/views/**', 'routes/**'],
            },
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/vue')) return 'vendor-vue'
                    if (id.includes('node_modules/@inertiajs')) return 'vendor-inertia'
                    if (id.includes('node_modules/axios')) return 'vendor-axios'
                    if (id.includes('vendor/tightenco/ziggy')) return 'vendor-ziggy'
                    if (id.includes('node_modules/@fontsource/montserrat')) return 'vendor-fonts'
                },
            },
        },
    },
});
