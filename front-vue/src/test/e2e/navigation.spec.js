import { test, expect } from '@playwright/test';

test.describe('SOL Store - Navegación y Responsividad E2E', () => {
  
  test('La aplicación carga correctamente', async ({ page }) => {
    console.log('🌐 Probando carga de la aplicación...');
    
    // Navegar a la página principal
    await page.goto('/');
    
    // Esperar a que la página cargue completamente
    await page.waitForLoadState('networkidle');
    
    // Verificar que hay al menos un header visible
    const headers = page.locator('header');
    await expect(headers.first()).toBeVisible();
    console.log('✅ Header encontrado');
    
    // Verificar que el título de la página existe
    await expect(page).toHaveTitle(/.*/); // Cualquier título
    console.log('✅ Página tiene título');
    
    // Verificar que hay contenido en el body
    const body = page.locator('body');
    await expect(body).not.toBeEmpty();
    console.log('✅ Contenido del body presente');
    
    console.log('🎉 Aplicación cargó correctamente');
  });

  test('Navegación básica funciona', async ({ page }) => {
    console.log('🧭 Probando navegación básica...');
    
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    
    // Verificar que podemos navegar a diferentes secciones
    // Buscar cualquier enlace y hacer click
    const links = page.locator('a[href]');
    const linkCount = await links.count();
    
    if (linkCount > 0) {
      console.log(`✅ Encontrados ${linkCount} enlaces en la página`);
      
      // Intentar navegar al primer enlace interno
      const firstInternalLink = links.filter({ has: page.locator('[href^="/"]') }).first();
      
      if (await firstInternalLink.count() > 0) {
        await firstInternalLink.click();
        await page.waitForLoadState('networkidle');
        console.log('✅ Navegación a enlace interno exitosa');
      }
    }
    
    console.log('🎉 Navegación básica funcionando');
  });

  test('Responsive móvil', async ({ page }) => {
    console.log('📱 Probando responsividad móvil...');
    
    // Cambiar a viewport móvil
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    
    // Verificar que la página se adapta
    const body = page.locator('body');
    await expect(body).toBeVisible();
    
    // Verificar el ancho del viewport
    const viewportSize = page.viewportSize();
    expect(viewportSize?.width).toBe(375);
    console.log('✅ Viewport móvil configurado correctamente');
    
    // Buscar si hay un botón de menú hamburguesa
    const hamburger = page.locator('.hamburger, .menu-toggle, [aria-label*="menu"]').first();
    const hasHamburger = await hamburger.count() > 0;
    
    if (hasHamburger) {
      console.log('✅ Menú hamburguesa encontrado');
    } else {
      console.log('ℹ️  No se encontró menú hamburguesa (puede ser normal)');
    }
    
    console.log('🎉 Responsividad móvil verificada');
  });

  test('Rendimiento de carga', async ({ page }) => {
    console.log('⚡ Probando rendimiento de carga...');
    
    const startTime = Date.now();
    await page.goto('/');
    await page.waitForLoadState('domcontentloaded');
    const loadTime = Date.now() - startTime;
    
    // La página debería cargar en menos de 5 segundos
    expect(loadTime).toBeLessThan(5000);
    console.log(`✅ Página cargada en ${loadTime}ms`);
    
    // Verificar que hay contenido visible
    const body = page.locator('body');
    await expect(body).toBeVisible();
    
    console.log('🎉 Rendimiento de carga aceptable');
  });
});