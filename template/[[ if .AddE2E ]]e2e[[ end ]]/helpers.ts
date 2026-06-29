// import { Page } from '@playwright/test';

/**
 * Credentials seeded by Database\Seeders\E2ETestSeeder (password "password").
 */
export const E2E_USER = { email: 'e2e@example.com', password: 'password' };

/**
 * Log a user in through the application's login form.
 *
 * A freshly scaffolded project ships without auth UI, so this helper is left
 * commented out as a starting point. Once you add authentication (Breeze,
 * Fortify, Filament, laravel/ui, ...), uncomment this and adapt the selectors
 * to match your login page, then use it from your specs:
 *
 *     import { E2E_USER, login } from './helpers';
 *     await login(page, E2E_USER);
 */
// export async function login(page: Page, user: { email: string; password: string }): Promise<void> {
//     await page.goto('/login');
//     await page.getByLabel('Email').fill(user.email);
//     await page.getByLabel('Password').fill(user.password);
//     await page.getByRole('button', { name: 'Log in' }).click();
// }
