# 🧪 Selenium oma projekti testimiseks

Projekt automatiseeritud testide jaoks, kasutades **Selenium WebDriverit** ja **Pytesti**.  
Eesmärk – testida oma veebiprojekti (sisselogimine, navigeerimine, vormid jms).

## ⚙️ Nõuded
- Python 3.9+  
- Google Chrome + [ChromeDriver](https://chromedriver.chromium.org/downloads)  
- Paigalda teegid:
  ```bash
  pip install -r requirements.txt


🚀 Käivitamine

Kõik testid:

pytest tests/


Üks test:

pytest tests/test_login.py

🧠 Näidis
from selenium import webdriver
from selenium.webdriver.common.by import By

def test_login():
    driver = webdriver.Chrome()
    driver.get("https://minu-veebileht.ee/login")
    driver.find_element(By.ID, "username").send_keys("test")
    driver.find_element(By.ID, "password").send_keys("1234")
    driver.find_element(By.ID, "login-button").click()
    assert "Tere tulemast" in driver.page_source
    driver.quit()
