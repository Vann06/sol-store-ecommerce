import { fileURLToPath } from 'node:url'
import { mergeConfig, defineConfig, configDefaults } from 'vitest/config'
import viteConfig from './vite.config'

export default mergeConfig(
  viteConfig,
  defineConfig({
    test: {
      environment: 'jsdom',
      setupFiles: ['./src/test/setupTests.js'],
      exclude: [
        ...configDefaults.exclude,
        'e2e/**', // Excluir Playwright
        '**/e2e/**', // Excluir cualquier carpeta e2e
        '**/*.e2e.spec.js', // Excluir archivos .e2e.spec.js
      ],
      root: fileURLToPath(new URL('./', import.meta.url)),
      globals: true,
    }
  })
)
