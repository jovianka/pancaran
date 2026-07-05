import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig(({ isSsrBuild }) => ({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
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
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    // Split the heavy PDF/editor vendors into their own cacheable chunks. The
    // pdfme sub-packages are split individually so the lightweight certificate
    // preview (pdfme/converter) never pulls in the multi-MB pdfme/ui designer.
    build: isSsrBuild
        ? {}
        : {
              rollupOptions: {
                  output: {
                      manualChunks(id: string) {
                          if (!id.includes('node_modules')) {
                              return;
                          }
                          if (id.includes('@pdfme/ui')) {
                              return 'pdfme-ui';
                          }
                          if (id.includes('@pdfme/generator')) {
                              return 'pdfme-generator';
                          }
                          if (id.includes('@pdfme/converter')) {
                              return 'pdfme-converter';
                          }
                          if (id.includes('@tiptap') || id.includes('prosemirror')) {
                              return 'tiptap';
                          }
                      },
                  },
              },
          },
}));
