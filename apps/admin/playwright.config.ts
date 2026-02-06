import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://localhost:4200';
const isLocalBaseUrl = /^https?:\/\/localhost(?::\d+)?/i.test(baseURL);
const shouldStartApi = process.env.PLAYWRIGHT_START_API === '1';

const webServer: Array<{
  command: string;
  url: string;
  reuseExistingServer?: boolean;
  timeout?: number;
  cwd?: string;
}> = [];

// Local runs: start the Vite dev server automatically.
if (isLocalBaseUrl) {
  webServer.push({
    command: 'pnpm dev --port 4200',
    url: baseURL,
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  });
}

// Optional: start the .NET API for local E2E runs.
// Note: the API still requires its dependencies (Postgres/Redis) to be available.
if (isLocalBaseUrl && shouldStartApi) {
  webServer.push({
    command: 'dotnet run --urls http://localhost:5001',
    url: 'http://localhost:5001/health',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
    cwd: '../api/CAU.Eleitoral.Api',
  });
}

export default defineConfig({
  testDir: './e2e',
  testIgnore: ['**/*.prod.spec.ts'],
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  webServer: webServer.length ? webServer : undefined,
});
