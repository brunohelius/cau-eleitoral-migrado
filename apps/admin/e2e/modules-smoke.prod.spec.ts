import { test, expect, type Page } from '@playwright/test';

const ADMIN_CREDENTIALS = {
  email: 'admin@cau.org.br',
  password: 'Admin@123',
};

async function login(page: Page) {
  await page.goto('/login');
  await page.getByRole('textbox', { name: 'Email' }).fill(ADMIN_CREDENTIALS.email);
  await page.getByRole('textbox', { name: 'Senha' }).fill(ADMIN_CREDENTIALS.password);
  await page.getByRole('button', { name: 'Entrar' }).click();
  await expect(page).toHaveURL(/dashboard/);
}

const CORE_PAGES: Array<{ path: string; heading: string }> = [
  { path: '/eleicoes', heading: 'Eleicoes' },
  { path: '/chapas', heading: 'Chapas' },
  { path: '/denuncias', heading: 'Denuncias' },
  { path: '/impugnacoes', heading: 'Impugnacoes' },
  { path: '/julgamentos', heading: 'Julgamentos' },
  { path: '/usuarios', heading: 'Usuarios' },
  { path: '/votacao', heading: 'Votacao' },
  { path: '/relatorios', heading: 'Relatorios' },
  { path: '/auditoria', heading: 'Auditoria' },
  { path: '/configuracoes', heading: 'Configuracoes' },
];

const AUX_PAGES: Array<{ path: string; heading: string }> = [
  { path: '/eleicoes/nova', heading: 'Nova Eleicao' },
  { path: '/chapas/nova', heading: 'Nova Chapa' },
  { path: '/denuncias/nova', heading: 'Nova Denuncia' },
  { path: '/impugnacoes/nova', heading: 'Nova Impugnacao' },
  { path: '/usuarios/novo', heading: 'Novo Usuario' },
  { path: '/julgamentos/sessao', heading: 'Sessao de Julgamento' },
  { path: '/relatorios/eleicao', heading: 'Relatorios de Eleicao' },
  { path: '/relatorios/votacao', heading: 'Relatorios de Votacao' },
];

test.describe('Admin Modules Smoke (Production)', () => {
  test('should load core module pages', async ({ page }) => {
    await login(page);

    for (const { path, heading } of CORE_PAGES) {
      await test.step(`${heading} (${path})`, async () => {
        await page.goto(path);
        await expect(
          page.getByRole('heading', { name: heading, level: 1, exact: true })
        ).toBeVisible({
          timeout: 15000,
        });
      });
    }
  });

  test('should render create/sub pages (no submits)', async ({ page }) => {
    await login(page);

    for (const { path, heading } of AUX_PAGES) {
      await test.step(`${heading} (${path})`, async () => {
        await page.goto(path);
        await expect(
          page.getByRole('heading', { name: heading, level: 1, exact: true })
        ).toBeVisible({
          timeout: 15000,
        });
      });
    }
  });
});
