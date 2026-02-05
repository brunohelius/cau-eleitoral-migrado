import { test, expect } from '@playwright/test';

test.describe('Elections Management', () => {
  test.beforeEach(async ({ page }) => {
    // Login before each test
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('admin@cau.org.br');
    await page.getByRole('textbox', { name: 'Senha' }).fill('Admin@123');
    await page.getByRole('button', { name: 'Entrar' }).click();
    await expect(page).toHaveURL(/dashboard/);

    // Navigate to elections
    await page.getByRole('link', { name: 'Eleicoes' }).click();
    await expect(page).toHaveURL(/eleicoes/);
  });

  test('should display elections list', async ({ page }) => {
    // Should have elections page heading
    await expect(page.getByRole('heading', { name: 'Eleicoes' })).toBeVisible();
  });

  test('should have navigation working', async ({ page }) => {
    // Can navigate back to dashboard
    await page.getByRole('link', { name: 'Dashboard' }).click();
    await expect(page).toHaveURL(/dashboard/);
  });
});
