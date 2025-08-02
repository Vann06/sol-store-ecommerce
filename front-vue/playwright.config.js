import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  outputDir: './test-results',
  timeout: 30 * 1000,
  expect: {
    timeout: 5000
  },
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: [
    ['list'],
    ['html', { outputFolder: './playwright-report' }]
  ],
  use: {
    actionTimeout: 0,
    baseURL: process.env.BASE_URL || 'http://frontend:5173',
    trace: 'on-first-retry',
    video: 'retain-on-failure',
    screenshot: 'only-on-failure',
    // Inyectar variables en el navegador
    launchOptions: {
      env: {
        VITE_WHATSAPP_PHONE: process.env.VITE_WHATSAPP_PHONE || '50231271096',
        VITE_WHATSAPP_MESSAGE: process.env.VITE_WHATSAPP_MESSAGE || 'Mensaje predeterminado'
      }
    }
  },
  projects: [
    {
      name: 'chromium',
      use: { 
        browserName: 'chromium',
        viewport: { width: 1920, height: 1080 }
      },
    }
  ],
  webServer: {
    command: 'npm run dev',
    url: process.env.BASE_URL || 'http://frontend:5173',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  },
});