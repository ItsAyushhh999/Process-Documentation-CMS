import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    server: {
        origin: 'http://dev.task-process.com',
        port: 3006,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/script.js', 'resources/js/app.js', 'resources/js/ajaxScript.js', 'resources/js/tiptap.js', 'resources/js/laravelApp.js'],
            refresh: true,
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
        commonjsOptions: {
            transformMixedEsModules: true
        },
    },
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js'
        },
    },
});
