import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import {BootstrapVueNextResolver} from "bootstrap-vue-next";
import Components from 'unplugin-vue-components/vite';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            clientPort: 443,
        },
        allowedHosts: true,
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
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
        Components({
            resolvers: [BootstrapVueNextResolver()],
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '@assets': path.resolve(__dirname, './resources/assets'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    build: {
        rollupOptions: {
            output:{
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        }
    }
});
