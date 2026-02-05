import { test, expect } from '@playwright/test';

// Full system tests for CAU Sistema Eleitoral
const BASE_URL = 'https://cau-admin.migrai.com.br';
const API_URL = 'https://cau-api.migrai.com.br';

const ADMIN_CREDENTIALS = {
  email: 'admin@cau.org.br',
  password: 'Admin@123'
};

test.describe('CAU Sistema Eleitoral - Full System Tests', () => {

  test.describe('Authentication', () => {
    test('should display login page', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      await expect(page.getByRole('heading', { name: /entrar/i })).toBeVisible();
      await expect(page.getByLabel(/email/i)).toBeVisible();
      await expect(page.getByLabel(/senha/i)).toBeVisible();
    });

    test('should show error with invalid credentials', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      await page.getByLabel(/email/i).fill('invalid@email.com');
      await page.getByLabel(/senha/i).fill('wrongpassword');
      await page.getByRole('button', { name: /entrar/i }).click();

      // Should show error message (use first() to handle multiple matches from toast)
      await expect(page.getByText('Erro ao fazer login', { exact: true })).toBeVisible({ timeout: 10000 });
    });

    test('should login successfully with valid credentials', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      await page.getByLabel(/email/i).fill(ADMIN_CREDENTIALS.email);
      await page.getByLabel(/senha/i).fill(ADMIN_CREDENTIALS.password);
      await page.getByRole('button', { name: /entrar/i }).click();

      // Should redirect to dashboard or home
      await expect(page).toHaveURL(/\/(dashboard|home)?$/, { timeout: 15000 });
    });
  });

  test.describe('Dashboard', () => {
    test.beforeEach(async ({ page }) => {
      // Login before each test
      await page.goto(`${BASE_URL}/login`);
      await page.getByLabel(/email/i).fill(ADMIN_CREDENTIALS.email);
      await page.getByLabel(/senha/i).fill(ADMIN_CREDENTIALS.password);
      await page.getByRole('button', { name: /entrar/i }).click();
      await page.waitForURL(/\/(dashboard|home)?$/, { timeout: 15000 });
    });

    test('should display dashboard after login', async ({ page }) => {
      // Check for dashboard heading
      await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible({ timeout: 10000 });
    });

    test('should have navigation menu', async ({ page }) => {
      // Check for navigation elements (use first() to handle multiple nav elements)
      await expect(page.getByRole('navigation').first()).toBeVisible();
    });
  });

  test.describe('Elections (Eleições)', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      await page.getByLabel(/email/i).fill(ADMIN_CREDENTIALS.email);
      await page.getByLabel(/senha/i).fill(ADMIN_CREDENTIALS.password);
      await page.getByRole('button', { name: /entrar/i }).click();
      await page.waitForURL(/\/(dashboard|home)?$/, { timeout: 15000 });
    });

    test('should navigate to elections page', async ({ page }) => {
      // Click on elections menu item (note: uses "Eleicoes" without accents)
      await page.getByRole('link', { name: /eleicoes/i }).first().click();
      await expect(page).toHaveURL(/eleic/i, { timeout: 30000 });
    });

    test('should display list of elections', async ({ page }) => {
      await page.goto(`${BASE_URL}/eleicoes`);
      await page.waitForLoadState('networkidle');
      // Should show elections heading (uses "Eleicoes" without accents)
      await expect(page.getByRole('heading', { name: /eleicoes/i })).toBeVisible({ timeout: 15000 });
    });
  });

  test.describe('Slates (Chapas)', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      await page.getByLabel(/email/i).fill(ADMIN_CREDENTIALS.email);
      await page.getByLabel(/senha/i).fill(ADMIN_CREDENTIALS.password);
      await page.getByRole('button', { name: /entrar/i }).click();
      await page.waitForURL(/\/(dashboard|home)?$/, { timeout: 15000 });
    });

    test('should navigate to slates page', async ({ page }) => {
      await page.getByRole('link', { name: /chapas/i }).first().click();
      await expect(page).toHaveURL(/chapa/i, { timeout: 10000 });
    });
  });

  test.describe('API Endpoints', () => {
    test('should return healthy status', async ({ request }) => {
      const response = await request.get(`${API_URL}/health`);
      expect(response.ok()).toBeTruthy();
      const body = await response.text();
      expect(body).toBe('Healthy');
    });

    test('should authenticate via API', async ({ request }) => {
      const response = await request.post(`${API_URL}/api/auth/login`, {
        data: ADMIN_CREDENTIALS
      });
      expect(response.ok()).toBeTruthy();
      const body = await response.json();
      expect(body.accessToken).toBeDefined();
    });

    test('should fetch elections via API', async ({ request }) => {
      // First login
      const loginResponse = await request.post(`${API_URL}/api/auth/login`, {
        data: ADMIN_CREDENTIALS
      });
      const { accessToken } = await loginResponse.json();

      // Then fetch elections
      const response = await request.get(`${API_URL}/api/eleicao`, {
        headers: {
          'Authorization': `Bearer ${accessToken}`
        }
      });
      expect(response.ok()).toBeTruthy();
      const elections = await response.json();
      expect(Array.isArray(elections)).toBeTruthy();
      expect(elections.length).toBeGreaterThan(0);
    });

    test('should fetch slates via API', async ({ request }) => {
      const loginResponse = await request.post(`${API_URL}/api/auth/login`, {
        data: ADMIN_CREDENTIALS
      });
      const { accessToken } = await loginResponse.json();

      const response = await request.get(`${API_URL}/api/chapa`, {
        headers: {
          'Authorization': `Bearer ${accessToken}`
        }
      });
      expect(response.ok()).toBeTruthy();
      const slates = await response.json();
      expect(Array.isArray(slates)).toBeTruthy();
    });
  });
});
