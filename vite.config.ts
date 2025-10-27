import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    resolve: {
        alias: {
          "@": fileURLToPath(new URL("./frontend/page-customizer-frontend/src", import.meta.url)),
        },
      },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 
                "frontend/page-customizer-frontend/src/style.css",
                "frontend/page-customizer-frontend/src/main.ts"
            ],
            refresh: true,
        }),
        vue(), 
        tailwindcss(),
    ],
});
