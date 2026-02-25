import { expect, test } from '@playwright/test'

async function expectNoHorizontalOverflow(page: any) {
  const hasHorizontalOverflow = await page.evaluate(() => {
    return document.documentElement.scrollWidth > window.innerWidth + 1
  })
  expect(hasHorizontalOverflow).toBeFalsy()
}

test.describe('Testes de Responsividade Public', () => {
  test('CT-RESP-004 Public Desktop 1920x1080 deve exibir home sem quebra', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 })
    await page.goto('/')

    await expect(page.getByRole('link', { name: /cau sistema eleitoral/i })).toBeVisible()
    await expectNoHorizontalOverflow(page)
  })

  test('CT-RESP-005 Public Mobile 375x667 deve manter navegacao funcional', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 })
    await page.goto('/')

    await expect(page.getByRole('link', { name: /cau sistema eleitoral/i })).toBeVisible()
    const areaEleitorLink = page.getByRole('link', { name: /[áa]rea do eleitor/i }).first()
    if (await areaEleitorLink.count()) {
      await expect(areaEleitorLink).toBeVisible()
    } else {
      await page.goto('/votacao')
      await expect(page.getByRole('heading', { name: /[áa]rea do eleitor/i })).toBeVisible()
    }
    await expectNoHorizontalOverflow(page)
  })
})
