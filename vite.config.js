import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// 1. นำเข้าปลั๊กอินของ Tailwind v4
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // 2. เรียกใช้งานปลั๊กอิน
        tailwindcss(),
    ],
});