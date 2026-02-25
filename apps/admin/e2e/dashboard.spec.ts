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
    await expect(page.getByRole('heading', { name: /Eleic/i })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Chapas Registradas' })).toBeVisible();
    await expect(page.getByRole('heading', { name: /Denunc/i })).toBeVisible();
    await expect(page.getByRole('heading', { name: /Impugn/i })).toBeVisible();
  });

  test('should navigate to elections page', async ({ page }) => {
    await page.goto('/eleicoes');
    await expect(page).toHaveURL(/eleicoes/);
  });

  test('should navigate to slates page', async ({ page }) => {
    await page.goto('/chapas');
    await expect(page).toHaveURL(/chapas/);
  });

  test('should navigate to users page', async ({ page }) => {
    await page.goto('/usuarios');
    await expect(page).toHaveURL(/usuarios/);
  });

  test('should display sidebar navigation', async ({ page }) => {
    // Navigation links
    await expect(page.getByRole('navigation').first()).toBeVisible();
    await expect(page.locator('a[href="/dashboard"]:visible').first()).toBeVisible();
    await expect(page.locator('a[href="/eleicoes"]:visible').first()).toBeVisible();
    await expect(page.locator('a[href="/chapas"]:visible').first()).toBeVisible();
    await expect(page.locator('a[href="/denuncias"]:visible').first()).toBeVisible();
    await expect(page.locator('a[href="/usuarios"]:visible').first()).toBeVisible();
  });

  test('should display elections in progress', async ({ page }) => {
    await expect(page.getByRole('heading', { name: /Elei.*Andamento/i })).toBeVisible();
  });
});
