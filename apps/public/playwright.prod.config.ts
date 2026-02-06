import { defineConfig, devices } from '@playwright/test';

// Production testing config - tests against deployed CloudFront URLs
export default defineConfig({
  testDir: './e2e',
  fullyParallel: true,
  forbidOnly: true,
  retries: 2,
  workers: 1,
  reporter: [['html', { outputFolder: 'playwright-report-prod' }], ['list']],
  timeout: 60000,
  use: {
    baseURL: 'https://cau-public.migrai.com.br',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    ignoreHTTPSErrors: true,
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
