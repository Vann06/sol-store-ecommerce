import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  // Directorio donde están las pruebas E2E
  testDir: './src/test/e2e',
  
  // Configuración global
  fullyParallel: false, // Cambiado a false para debugging
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : 1, // Solo 1 worker para debugging
  
  // Timeout aumentado para desarrollo
  timeout: 30 * 1000,
  
  // Configuración del reporter
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['list'],
    ['json', { outputFile: 'test-results/results.json' }]
  ],
  
  // Configuración global de pruebas
  use: {
    // URL base de tu aplicación Vue (configurable por env)
    baseURL: process.env.PW_BASE_URL || 'http://localhost:5173',
    
    // Capturar screenshots en fallos
    screenshot: 'only-on-failure',
    
    // Grabar videos en fallos
    video: 'retain-on-failure',
    
    // Trazas para debugging
    trace: 'on-first-retry',
    
    // Headers globales
    extraHTTPHeaders: {
      'Accept': 'application/json'
    },
    
    // Viewport por defecto
    viewport: { width: 1280, height: 720 },
    
    // Ignorar errores HTTPS en desarrollo
    ignoreHTTPSErrors: true,
  },

  // Configuración de proyectos/browsers
  // SOLO CHROMIUM para desarrollo local simplificado
  projects: [
    {
      name: 'chromium',
      use: { 
        ...devices['Desktop Chrome'],
        // Opciones adicionales para Chromium
        launchOptions: {
          args: ['--no-sandbox', '--disable-setuid-sandbox']
        }
      },
    },
    
    // Descomenta estos cuando los necesites:
    // {
    //   name: 'firefox',
    //   use: { ...devices['Desktop Firefox'] },
    // },
    // {
    //   name: 'webkit',
    //   use: { ...devices['Desktop Safari'] },
    // },
    // {
    //   name: 'Mobile Chrome',
    //   use: { ...devices['Pixel 5'] },
    // },
    // {
    //   name: 'Mobile Safari',
    //   use: { ...devices['iPhone 12'] },
    // },
  ],

  // Servidor de desarrollo (para pruebas locales)
  // Deshabilitado porque ya está corriendo en Docker
  // webServer: {
  //   command: 'npm run dev',
  //   url: 'http://localhost:5173',
  //   reuseExistingServer: true,
  //   timeout: 120 * 1000,
  // },
});
