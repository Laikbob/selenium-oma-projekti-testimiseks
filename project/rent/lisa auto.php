<?php
if (isset($_GET['code'])) {
    die(highlight_file(__FILE__, 1));
}

session_start();
require('zoneconf.php');
global $yhendus;

// Проверка доступа: только админ
if (!isset($_SESSION['onadmin']) || $_SESSION['onadmin'] != 1) {
    header("Location: login.php");
    exit();
}

$success = "";

// Обработка формы добавления авто
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $mark = $yhendus->real_escape_string(trim($_POST['mark']));
    $mudel = $yhendus->real_escape_string(trim($_POST['mudel']));
    $aasta = (int)$_POST['aasta'];
    $status = $yhendus->real_escape_string(trim($_POST['status']));
    $url_photo = $yhendus->real_escape_string(trim($_POST['url_photo']));

    $yhendus->query("INSERT INTO auto (Mark, Mudel, Aasta, Status) VALUES ('$mark', '$mudel', $aasta, '$status')");
    $auto_id = $yhendus->insert_id;

    if (!empty($url_photo)) {
        $yhendus->query("DELETE FROM pilt WHERE AutoID = $auto_id");
        $yhendus->query("INSERT INTO pilt (AutoID, URL_photo) VALUES ($auto_id, '$url_photo')");
    }

    $success = "Auto lisatud edukalt!";
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Admin – Lisa Auto</title>
    <link rel="stylesheet" href="autolisamine.css">
</head>
<body>
<div class="main-container">
    <h2>Tere tulemast Auto Renti</h2>

    <div class="user-info">
        <p>
            Tere, <?= htmlspecialchars($_SESSION['kasutaja'] ?? 'kasutaja') ?>
        </p>
        <form method="post" action="logout.php">
            <input type="submit" value="Logi välja">
        </form>
    </div>

    <nav>
        <ul>
            <li><a href="index.php">Autod</a></li>
            <li><a href="lisa%20auto.php">Auto Lisamine</a></li>
        </ul>
    </nav>

    <h2>Lisa uus auto</h2>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Mark: <input type="text" name="mark" required></label><br>
        <label>Mudel: <input type="text" name="mudel" required></label><br>
        <label>Aasta: <input type="number" name="aasta" required></label><br>
        <label>Staatus:
            <select name="status" required>
                <option value="vaba">vaba</option>
                <option value="broneeritud">broneeritud</option>
                <option value="hooldus">hooldus</option>
            </select>
        </label><br>
        <label>Foto URL: <input type="url" name="url_photo"></label><br>
        <input type="submit" name="add_car" value="Lisa auto">
    </form>
</div>
</body>
</html>


