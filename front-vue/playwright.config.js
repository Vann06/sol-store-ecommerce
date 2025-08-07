import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  // Directorio donde están las pruebas E2E
  testDir: './src/test/e2e',
  
  // Configuración global
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  
  // Configuración del reporter
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['json', { outputFile: 'test-results/results.json' }],
    ['list']
  ],
  
  // Configuración global de pruebas
  use: {
    // URL base de tu aplicación Vue
    baseURL: 'http://localhost:3000',
    
    // Capturar screenshots en fallos
    screenshot: 'only-on-failure',
    
    // Grabar videos en fallos
    video: 'retain-on-failure',
    
    // Trazas para debugging
    trace: 'on-first-retry',
    
    // Headers globales
    extraHTTPHeaders: {
      'Accept': 'application/json'
    }
  },

  // Configuración de proyectos/browsers
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
    // Pruebas móviles
    {
      name: 'Mobile Chrome',
      use: { ...devices['Pixel 5'] },
    },
    {
      name: 'Mobile Safari',
      use: { ...devices['iPhone 12'] },
    },
  ],

  // Servidor de desarrollo (para pruebas locales)
  webServer: {
    command: 'npm run dev',
    url: 'http://localhost:3000',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  },
});