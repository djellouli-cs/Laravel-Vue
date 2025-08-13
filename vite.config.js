import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const host = env.VITE_HOST || 'localhost';

    return {
        server: {
            host,
            port: 5173,
            cors: true,
            strictPort: true,
            hmr: {
                host,
                port: 5173,
                protocol: 'ws',
            },
        },
        plugins: [
            vue(),
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
    };
});
