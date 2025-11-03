import { test, expect } from '@playwright/test';

test('Главная страница аренды загружается', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/index.php');
  await expect(page).toHaveTitle(/Rent|Аренда/i);
});

test('Проверка наличия заголовка', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/index.php');
  const heading = await page.textContent('h1');
  expect(heading).not.toBeNull();
});

test('Вход с логином и паролем', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/loginrent.php');

  await page.fill('input[name="username"]', 'kasutaja'); 

  await page.fill('input[name="password"]', 'kasutaja'); 

  await page.click('button[type="submit"]'); 

  await expect(page.locator('text=Tere tulemast')).toBeVisible();
});

test('Фильтр автомобилей работает', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/index.php');

  await page.fill('input[name="mark"]', 'Toyota');
  await page.fill('input[name="mudel"]', 'Corolla');

  await page.selectOption('select[name="status"]', 'vaba');

  await page.click('button[type="submit"]');

  const cars = page.locator('.car-name'); 
  const count = await cars.count();

  expect(count).toBeGreaterThan(0);

  for (let i = 0; i < count; i++) {
    await expect(cars.nth(i)).toContainText(/Toyota/i);
  }
});

test('Бронирование автомобиля работает корректно', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/index.php');

  const formSelector = 'form[action*="rent"][method="POST"]'; 

  await expect(page.locator(formSelector)).toHaveCountGreaterThan(0);

  await page.click('button[name="book_car"]');

  await expect(page.locator('text=broneeritud')).toBeVisible({ timeout: 5000 });
});


