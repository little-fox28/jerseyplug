import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ command }) => {
    const isBuild = command === 'build';
    const themeDirName = 'jerseyplug';
    const localDomain = 'http://jerseyplug.local';

    return {
        base: isBuild ? `/wp-content/themes/${themeDirName}/dist/` : '/',
        server: {
            host: 'localhost',
            port: 3000,
            strictPort: true,
            cors: true,
            origin: localDomain,
            hmr: {
                host: 'localhost',
            }
        },
        build: {
            manifest: true,
            outDir: 'dist',
            rollupOptions: {
                input: [
                    'resources/js/app.js',
                    'resources/js/products-filter.js',
                    'resources/css/app.css',
                    'resources/css/editor-style.css'
                ],
            },
        },
        plugins: [
            tailwindcss(),
        ],
    }
});