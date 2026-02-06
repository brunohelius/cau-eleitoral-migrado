import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://localhost:4201';
const isLocalBaseUrl = /^https?:\/\/localhost(?::\d+)?/i.test(baseURL);
const shouldStartApi = process.env.PLAYWRIGHT_START_API === '1';

const webServer: Array<{
  command: string;
  url: string;
  reuseExistingServer?: boolean;
  timeout?: number;
  cwd?: string;
}> = [];

if (isLocalBaseUrl) {
  webServer.push({
    command: 'pnpm dev --port 4201',
    url: baseURL,
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  });
}

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
