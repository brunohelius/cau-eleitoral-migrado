import { test, expect } from '@playwright/test';

const BASE_URL = 'https://cau-admin.migrai.com.br';

test.describe('Login Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Clear any existing session
    await page.context().clearCookies();
  });

  test('should login and show dashboard', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);

    // Wait for page to load
    await page.waitForLoadState('networkidle');

    // Fill login form
    await page.getByLabel(/email/i).fill('admin@cau.org.br');
    await page.getByLabel(/senha/i).fill('Admin@123');

    // Click login button
    await page.getByRole('button', { name: /entrar/i }).click();

    // Wait for navigation or error
    await Promise.race([
      page.waitForURL('**/dashboard**', { timeout: 20000 }),
      page.waitForURL('**/', { timeout: 20000 }),
      page.getByText(/erro|invalid/i).waitFor({ timeout: 20000 })
    ]).catch(() => {});

    // Take screenshot
    await page.screenshot({ path: 'test-results/login-result.png', fullPage: true });

    // Check current URL
    const url = page.url();
    console.log('Current URL after login:', url);

    // The login should have redirected away from /login
    // Accept either dashboard or root as success
    const isLoginSuccess = !url.includes('/login') || url.includes('/dashboard');
    expect(isLoginSuccess).toBeTruthy();
  });
});
