import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/frontpage.css',
                'resources/css/animations.css', // Add the new animations CSS file
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/page-transitions.js', // Add the new page transitions JS file
                'resources/js/products.js',
                'resources/js/script.js',
                'resources/css/check.css',
                'resources/css/dashboard.css'

            ],
            refresh: true,
            publicDirectory: 'public',
        }),
    ],

    server: {
    host: '0.0.0.0',
    hmr: {
        host: 'localhost',
    },
},
});
