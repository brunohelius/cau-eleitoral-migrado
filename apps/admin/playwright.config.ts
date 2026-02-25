import { defineConfig, devices } from '@playwright/test';

const ADMIN_TEST_PORT = process.env.PLAYWRIGHT_WEB_PORT || '7777';
const API_TEST_PORT = process.env.PLAYWRIGHT_API_PORT || '7779';
const baseURL = process.env.PLAYWRIGHT_BASE_URL || `http://localhost:${ADMIN_TEST_PORT}`;
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
    command: `VITE_API_URL=http://localhost:${API_TEST_PORT}/api VITE_PROXY_API_TARGET=http://localhost:${API_TEST_PORT} pnpm dev --port ${ADMIN_TEST_PORT}`,
    url: baseURL,
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  });
}

// Optional: start the .NET API for local E2E runs.
// Note: the API still requires its dependencies (Postgres/Redis) to be available.
if (isLocalBaseUrl && shouldStartApi) {
  webServer.push({
    command: `dotnet run --urls http://localhost:${API_TEST_PORT}`,
    url: `http://localhost:${API_TEST_PORT}/health`,
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
