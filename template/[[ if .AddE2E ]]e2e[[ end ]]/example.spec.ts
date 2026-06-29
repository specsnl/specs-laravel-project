import { test, expect } from '@playwright/test';

test.describe('Smoke', () => {
    test('welcome page loads', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveTitle(/[[ .ProjectName ]]|Laravel/);
    });
});
