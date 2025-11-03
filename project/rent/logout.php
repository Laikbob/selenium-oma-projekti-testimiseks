<?php
session_start();
session_destroy(); // Удаляет все данные сессии
header("Location: loginrent.php"); // Перенаправляет на страницу логина (замени на нужную)
exit();