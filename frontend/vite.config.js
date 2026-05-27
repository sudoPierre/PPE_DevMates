import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Configuration Vite pour DevMates
// En développement, le proxy redirige /api vers le backend PHP local
export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
    },
  },
});
