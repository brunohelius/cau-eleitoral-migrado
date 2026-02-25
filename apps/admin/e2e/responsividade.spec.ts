import { expect, test } from '@playwright/test'

async function expectNoHorizontalOverflow(page: any) {
  const hasHorizontalOverflow = await page.evaluate(() => {
    return document.documentElement.scrollWidth > window.innerWidth + 1
  })
  expect(hasHorizontalOverflow).toBeFalsy()
}

test.describe('Testes de Responsividade Admin', () => {
  test('CT-RESP-001 Admin Desktop 1920x1080 deve renderizar sem quebra de layout', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 })
    await page.goto('/login')

    await expect(page.getByRole('heading', { name: /entrar/i })).toBeVisible()
    await expect(page.getByRole('textbox', { name: /email/i })).toBeVisible()
    await expect(page.getByRole('textbox', { name: /senha/i })).toBeVisible()
    await expectNoHorizontalOverflow(page)
  })

  test('CT-RESP-002 Admin Tablet 768x1024 deve manter usabilidade no formulario', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 })
    await page.goto('/login')

    await expect(page.getByRole('heading', { name: /entrar/i })).toBeVisible()
    await expect(page.getByRole('textbox', { name: /email/i })).toBeVisible()
    await expect(page.getByRole('textbox', { name: /senha/i })).toBeVisible()
    await expect(page.getByRole('button', { name: /entrar/i })).toBeVisible()
    await expectNoHorizontalOverflow(page)
  })

  test('CT-RESP-003 Admin Mobile 375x667 deve funcionar sem overflow horizontal', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 })
    await page.goto('/login')

    await expect(page.getByRole('heading', { name: /entrar/i })).toBeVisible()
    await expect(page.getByRole('textbox', { name: /email/i })).toBeVisible()
    await expect(page.getByRole('textbox', { name: /senha/i })).toBeVisible()
    await expect(page.getByRole('button', { name: /entrar/i })).toBeVisible()
    await expectNoHorizontalOverflow(page)
  })
})
