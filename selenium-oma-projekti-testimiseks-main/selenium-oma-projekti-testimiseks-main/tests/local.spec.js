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

  // Ввод логина
  await page.fill('input[name="username"]', 'kasutaja'); // замените на реальный логин
  // Ввод пароля
  await page.fill('input[name="password"]', 'kasutaja'); // замените на реальный пароль

  // Нажатие кнопки входа
  await page.click('button[type="submit"]'); // или другой селектор кнопки

  // Проверка успешного входа (например, по наличию текста "Вы вошли" или личного кабинета)
  await expect(page.locator('text=Добро пожаловать')).toBeVisible();
});

test('Фильтр автомобилей работает', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/index.php');

  // Ввод фильтра (например, по марке)
  await page.fill('input[name="search"]', 'Toyota'); // селектор и значение фильтра
  await page.click('button[type="submit"]'); // кнопка поиска

  // Проверка, что отображаются только автомобили с Toyota
  const cars = page.locator('.car-name'); // замените на реальный селектор названия автомобиля
  const count = await cars.count();
  for (let i = 0; i < count; i++) {
    await expect(cars.nth(i)).toContainText('Toyota');
  }
});

test('Бронирование автомобиля', async ({ page }) => {
  await page.goto('https://andreilebedev24.thkit.ee/WEB/WEBphp/rent/index.php');

  // Нажатие на кнопку бронирования первого автомобиля
  await page.click('.book-button'); // замените на селектор кнопки "Забронировать"

  // Проверка успешного бронирования
  await expect(page.locator('.status')).toHaveText(/Забронирован/i); // замените на селектор статуса
});

