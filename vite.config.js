import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
            publicDirectory: "public",
        }),
    ],
    build: {
        // Ensure assets are built with correct public path
        outDir: "public/build",
        emptyOutDir: true,
        manifest: "manifest.json", // Put manifest directly in build folder
        rollupOptions: {
            // Don't include external dependencies in the bundle
            external: [],
        },
    },
    base: "/build/",
});
