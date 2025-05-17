import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/frontpage.css',
                'resources/css/animations.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/page-transitions.js',
                'resources/js/products.js',
                'resources/js/script.js',
                'resources/css/check.css',
                'resources/css/dashboard.css',
                // Remove the 'resources/images/' entry - this is not how images are handled
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
    
    // Add this section to properly handle image assets
    build: {
        assetsInlineLimit: 0, // Ensures images are processed as files rather than being inlined
    },
    
    // Configure asset handling
    resolve: {
        alias: {
            '@': '/resources', // This allows you to use @/images/... in your imports
        },
    },
});
