import { test, expect } from '@playwright/test';

test.describe('Public Voting Flow', () => {
  test('should display home page', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('link', { name: 'CAU Sistema Eleitoral' })).toBeVisible();
  });

  test('should navigate to voter login', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('banner').getByRole('link', { name: 'Area do Eleitor' }).click();
    await expect(page).toHaveURL(/votacao/);
  });

  test('should display voter login form', async ({ page }) => {
    await page.goto('/votacao');
    await expect(page.getByRole('heading', { name: 'Area do Eleitor' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'CPF' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Registro CAU' })).toBeVisible();
  });

  test('should enable continue button after filling CPF and CAU', async ({ page }) => {
    await page.goto('/votacao');

    // Fill CPF
    await page.getByRole('textbox', { name: 'CPF' }).fill('60000000003');
    // Fill Registro CAU
    await page.getByRole('textbox', { name: 'Registro CAU' }).fill('A000005SP');

    // Button should be enabled
    const continueButton = page.getByRole('button', { name: 'Continuar' });
    await expect(continueButton).toBeEnabled({ timeout: 3000 });
  });

  test('should display elections list on public page', async ({ page }) => {
    await page.goto('/eleicoes');
    await expect(page.getByRole('heading', { name: 'Eleicoes', exact: true })).toBeVisible();
  });

  test('should display calendar page', async ({ page }) => {
    await page.goto('/calendario');
    await expect(page.getByRole('heading', { name: /Calendario/i })).toBeVisible();
  });

  test('should display documents page', async ({ page }) => {
    await page.goto('/documentos');
    await expect(page.getByRole('heading', { name: 'Documentos Publicos' })).toBeVisible();
  });

  test('should display FAQ page', async ({ page }) => {
    await page.goto('/faq');
    await expect(page.getByRole('heading', { name: 'Perguntas Frequentes' })).toBeVisible();
  });
});

test.describe('Candidate Portal', () => {
  test('should display candidate login page', async ({ page }) => {
    await page.goto('/candidato/login');
    await expect(page.getByRole('textbox', { name: 'CPF' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Registro CAU' })).toBeVisible();
  });
});
