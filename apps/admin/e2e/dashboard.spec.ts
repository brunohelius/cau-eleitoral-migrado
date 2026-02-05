import { test, expect } from '@playwright/test';

test.describe('Admin Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    // Login before each test
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('admin@cau.org.br');
    await page.getByRole('textbox', { name: 'Senha' }).fill('Admin@123');
    await page.getByRole('button', { name: 'Entrar' }).click();
    await expect(page).toHaveURL(/dashboard/);
  });

  test('should display dashboard statistics', async ({ page }) => {
    // Check for statistics cards
    await expect(page.getByRole('heading', { name: 'Eleicoes Ativas' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Chapas Registradas' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Denuncias Pendentes' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Impugnacoes' })).toBeVisible();
  });

  test('should navigate to elections page', async ({ page }) => {
    await page.getByRole('link', { name: 'Eleicoes' }).click();
    await expect(page).toHaveURL(/eleicoes/);
  });

  test('should navigate to slates page', async ({ page }) => {
    await page.getByRole('link', { name: 'Chapas' }).click();
    await expect(page).toHaveURL(/chapas/);
  });

  test('should navigate to users page', async ({ page }) => {
    await page.getByRole('link', { name: 'Usuarios' }).click();
    await expect(page).toHaveURL(/usuarios/);
  });

  test('should display sidebar navigation', async ({ page }) => {
    // Navigation links
    await expect(page.getByRole('link', { name: 'Dashboard' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Eleicoes' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Chapas' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Denuncias' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Usuarios' })).toBeVisible();
  });

  test('should display elections in progress', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Eleicoes em Andamento' })).toBeVisible();
  });
});
