import { test, expect } from '@playwright/test'

const ADMIN_CREDENTIALS = {
  email: 'admin@cau.org.br',
  password: 'Admin@123',
}

async function login(page: import('@playwright/test').Page) {
  await page.goto('/login')
  await page.getByRole('textbox', { name: 'Email' }).fill(ADMIN_CREDENTIALS.email)
  await page.getByRole('textbox', { name: 'Senha' }).fill(ADMIN_CREDENTIALS.password)
  await page.getByRole('button', { name: 'Entrar' }).click()
  await expect(page).toHaveURL(/dashboard/, { timeout: 15000 })
}

test.describe('Admin Modules (Local/API)', () => {
  test.beforeEach(async ({ page }) => {
    await login(page)
  })

  test('should load Auditoria page', async ({ page }) => {
    await page.goto('/auditoria')
    await expect(page.getByRole('heading', { name: 'Auditoria', exact: true })).toBeVisible({ timeout: 15000 })
    await expect(page.getByRole('button', { name: 'Atualizar' })).toBeVisible()
  })

  test('should load Julgamentos list and open a detail page', async ({ page }) => {
    await page.goto('/julgamentos')
    await expect(page.getByRole('heading', { name: 'Julgamentos' })).toBeVisible({ timeout: 15000 })

    // Open first julgamento via table action (eye icon)
    const firstRowLink = page.locator('tbody a[href^="/julgamentos/"]').first()
    await expect(firstRowLink).toBeVisible({ timeout: 15000 })
    await firstRowLink.click()

    await expect(page).toHaveURL(/\/julgamentos\//, { timeout: 15000 })
    await expect(page.getByRole('heading', { name: 'Detalhes do Julgamento' })).toBeVisible({ timeout: 15000 })
  })

  test('should load Relatorios pages and allow selecting an eleicao', async ({ page }) => {
    await page.goto('/relatorios/eleicao')
    await expect(page.getByRole('heading', { name: 'Relatorios de Eleicao' })).toBeVisible({ timeout: 15000 })

    const eleicaoSelect = page.locator('select').first()
    await expect(eleicaoSelect).toBeVisible()
    await eleicaoSelect.selectOption({ index: 1 })

    const selectedEleicaoName = (await eleicaoSelect.locator('option:checked').textContent())?.trim() || ''
    expect(selectedEleicaoName.length).toBeGreaterThan(0)
    await expect(page.getByRole('heading', { name: selectedEleicaoName, exact: true })).toBeVisible({ timeout: 15000 })

    await page.goto('/relatorios/votacao')
    await expect(page.getByRole('heading', { name: 'Relatorios de Votacao' })).toBeVisible({ timeout: 15000 })
    const votacaoSelect = page.locator('select').first()
    await votacaoSelect.selectOption({ index: 1 })

    const hasData = page.getByText('Total de Votos', { exact: true })
    const noData = page.getByText('Nenhum dado de apuracao disponivel para esta eleicao', { exact: true })
    await Promise.race([
      hasData.waitFor({ state: 'visible', timeout: 15000 }),
      noData.waitFor({ state: 'visible', timeout: 15000 }),
    ])
  })

  test('should load Eleicao calendario page for first eleicao', async ({ page }) => {
    await page.goto('/eleicoes')
    await expect(page.getByRole('heading', { name: /Eleicoes/i })).toBeVisible({ timeout: 15000 })

    const firstDetailLink = page.locator('tbody a[href^="/eleicoes/"]').first()
    const href = await firstDetailLink.getAttribute('href')
    expect(href).toBeTruthy()

    const parts = (href || '').split('/')
    const eleicaoId = parts[2]
    expect(eleicaoId).toBeTruthy()

    await page.goto(`/eleicoes/${eleicaoId}/calendario`)
    await expect(page.getByRole('heading', { name: 'Calendario da Eleicao' })).toBeVisible({ timeout: 15000 })
    await expect(page.getByRole('button', { name: 'Novo Evento' })).toBeVisible()
  })

  test('should render Sessao de Julgamento page', async ({ page }) => {
    await page.goto('/julgamentos/sessao')
    await expect(page.getByRole('heading', { name: 'Sessao de Julgamento' })).toBeVisible({ timeout: 15000 })
    await expect(page.getByText('Pauta da Sessao', { exact: true })).toBeVisible({ timeout: 15000 })
  })
})
