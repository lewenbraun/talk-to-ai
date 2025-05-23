import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
  build: {
    outDir: "./public/build/",
  },
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  server: {
    host: "0.0.0.0",
    hmr: {
      host: "localhost",
    },
  },
  resolve: {
    alias: {
      vue: "vue/dist/vue.esm-bundler.js",
      "@": "/resources/js",
    },
  },
});
