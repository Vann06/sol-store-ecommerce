import { test, expect } from '@playwright/test';

test.describe('SOL Store - Navegación y Responsividad E2E', () => {
  
  test('Navegación desktop completa', async ({ page }) => {
    console.log('🧭 Probando navegación desktop...');
    
    await page.goto('/');
    
    // Verificar elementos del header
    await expect(page.locator('img[alt*="Logo"]')).toBeVisible();
    await expect(page.locator('input[placeholder*="Buscar"]')).toBeVisible();
    
    // Navegar por las páginas principales
    const pages = [
      { link: 'Sobre Nosotros', url: '/about' },
      { link: 'FAQ', url: '/faq' },
      { link: 'Carrito', url: '/cart' }
    ];
    
    for (const { link, url } of pages) {
      await page.click(`text=${link}`);
      await expect(page).toHaveURL(url);
      console.log(`✅ Navegación a ${link} exitosa`);
    }
    
    // Verificar footer
    await expect(page.locator('text=derechos reservados')).toBeVisible();
    
    console.log('🎉 Navegación desktop completa exitosa');
  });

  test('Responsividad móvil', async ({ page }) => {
    console.log('📱 Probando responsividad móvil...');
    
    // Cambiar a viewport móvil
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/');
    
    // Verificar que el contenido se adapta
    const header = page.locator('header');
    await expect(header).toBeVisible();
    
    // En móvil, podría haber un menú hamburguesa
    const menuButton = page.locator('[data-testid="mobile-menu"], .menu-toggle, .hamburger');
    
    if (await menuButton.isVisible()) {
      await menuButton.click();
      console.log('✅ Menú móvil desplegado');
    }
    
    console.log('✅ Responsividad móvil verificada');
  });

  test('Rendimiento y carga de página', async ({ page }) => {
    console.log('⚡ Probando rendimiento...');
    
    const startTime = Date.now();
    await page.goto('/');
    const loadTime = Date.now() - startTime;
    
    // La página debería cargar en menos de 5 segundos
    expect(loadTime).toBeLessThan(5000);
    
    // Verificar que los elementos críticos están presentes
    await expect(page.locator('header')).toBeVisible();
    await expect(page.locator('main, .main-content')).toBeVisible();
    await expect(page.locator('footer')).toBeVisible();
    
    console.log(`✅ Página cargada en ${loadTime}ms`);
  });
});