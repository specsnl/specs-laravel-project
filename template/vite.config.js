import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        cors: {
            origin: [
                'http://[[ .ProjectShortName | toKebabCase ]].local',
                'https://[[ .ProjectShortName | toKebabCase ]].local',
            ],
        }
    },
});
