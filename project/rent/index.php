<?php if (isset($_GET['code'])) { die(highlight_file(__FILE__, 1)); } ?>
<?php
session_start();
require('zoneconf.php');
global $yhendus;

$onadmin = $_SESSION['onadmin'] ?? 0;
$successMessage = '';
$errorMessage = '';

// --- Обработка POST-запросов ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($onadmin == 1) {
        // Добавление авто
        if (isset($_POST['add_car'])) {
            $mark = $yhendus->real_escape_string($_POST['mark']);
            $mudel = $yhendus->real_escape_string($_POST['mudel']);
            $aasta = (int)$_POST['aasta'];
            $status = $yhendus->real_escape_string($_POST['status']);
            $url_photo = $yhendus->real_escape_string($_POST['url_photo']);

            $yhendus->query("INSERT INTO auto (Mark, Mudel, Aasta, Status) VALUES ('$mark', '$mudel', $aasta, '$status')");
            $auto_id = $yhendus->insert_id;

            if (!empty($url_photo)) {
                $yhendus->query("INSERT INTO pilt (AutoID, URL_photo) VALUES ($auto_id, '$url_photo')");
            }
        }

        // Удаление авто
        if (isset($_POST['delete_car'])) {
            $auto_id = (int)$_POST['auto_id'];
            $yhendus->query("DELETE FROM pilt WHERE AutoID=$auto_id");
            $yhendus->query("DELETE FROM auto WHERE AutoID=$auto_id");
        }

        // Обновление статуса
        if (isset($_POST['update_status'])) {
            $auto_id = (int)$_POST['auto_id'];
            $new_status = $yhendus->real_escape_string($_POST['new_status']);
            $yhendus->query("UPDATE auto SET Status='$new_status' WHERE AutoID=$auto_id");
        }
    }

    // Бронирование авто (только пользователи)
    if ($onadmin == 0 && isset($_POST['book_car'])) {
        $auto_id = (int)$_POST['auto_id'];
        $res = $yhendus->query("SELECT Status FROM auto WHERE AutoID=$auto_id");
        if ($res && $row = $res->fetch_assoc()) {
            if ($row['Status'] === 'vaba') {
                $yhendus->query("UPDATE auto SET Status='broneeritud' WHERE AutoID=$auto_id");
                $successMessage = "Aitäh! Auto on edukalt broneeritud. Töötaja helistab teile";
            } else {
                $errorMessage = "Vabandame, see auto ei ole broneeritav.";
            }
        }
    }

    // Выход
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: logout.php');
        exit();
    }
}

// --- Фильтрация по GET ---
$conditions = [];

if (!empty($_GET['mark'])) {
    $mark = $yhendus->real_escape_string($_GET['mark']);
    $conditions[] = "auto.Mark LIKE '%$mark%'";
}
if (!empty($_GET['mudel'])) {
    $mudel = $yhendus->real_escape_string($_GET['mudel']);
    $conditions[] = "auto.Mudel LIKE '%$mudel%'";
}
if (!empty($_GET['status'])) {
    $status = $yhendus->real_escape_string($_GET['status']);
    $conditions[] = "auto.Status = '$status'";
}

$where = '';
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "SELECT auto.AutoID, auto.Mark, auto.Mudel, auto.Aasta, auto.Status, pilt.URL_photo
        FROM auto
        LEFT JOIN pilt ON auto.AutoID = pilt.AutoID
        $where";
$result = $yhendus->query($sql);
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Auto Rent</title>
    <link rel="stylesheet" href="rentstyle.css">
</head>
<body>
<div class="main-container">
    <h2>Tere tulemast Auto Renti</h2>

    <?php if (!empty($successMessage)): ?>
        <div class="success-message"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="error-message"><?= $errorMessage ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['onadmin'])): ?>
        <div class="user-info">
            <p>Tere, <?= htmlspecialchars($_SESSION['kasutaja'] ?? 'kasutaja') ?></p>
            <form method="post">
                <input type="submit" name="logout" value="Logi välja">
            </form>
        </div>

        <?php if ($onadmin): ?>
            <nav>
                <ul>
                    <li><a href="index.php">Autod</a></li>
                    <li><a href="lisa%20auto.php">Auto Lisamine</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <form method="GET" class="search-form">
        <input type="text" name="mark" placeholder="Otsi mark..." value="<?= htmlspecialchars($_GET['mark'] ?? '') ?>">
        <input type="text" name="mudel" placeholder="Otsi mudel..." value="<?= htmlspecialchars($_GET['mudel'] ?? '') ?>">
        <select name="status">
            <option value="">Kõik staatused</option>
            <option value="vaba" <?= ($_GET['status'] ?? '') === 'vaba' ? 'selected' : '' ?>>vaba</option>
            <option value="broneeritud" <?= ($_GET['status'] ?? '') === 'broneeritud' ? 'selected' : '' ?>>broneeritud</option>
            <option value="hooldus" <?= ($_GET['status'] ?? '') === 'hooldus' ? 'selected' : '' ?>>hooldus</option>
        </select>
        <button type="submit">Otsi</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="car-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="car-card">
                    <?php if (!empty($row["URL_photo"])): ?>
                        <img class="car-photo" src="<?= htmlspecialchars($row["URL_photo"]) ?>" alt="Auto pilt">
                    <?php else: ?>
                        <div class="no-photo">Foto puudub</div>
                    <?php endif; ?>

                    <div class="car-info">
                        <p><strong>Mark:</strong> <?= htmlspecialchars($row["Mark"]) ?></p>
                        <p><strong>Mudel:</strong> <?= htmlspecialchars($row["Mudel"]) ?></p>
                        <p><strong>Aasta:</strong> <?= htmlspecialchars($row["Aasta"]) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($row["Status"]) ?></p>

                        <?php if ($onadmin): ?>
                            <form method="POST">
                                <input type="hidden" name="auto_id" value="<?= $row['AutoID'] ?>">
                                <select name="new_status">
                                    <option value="vaba" <?= $row["Status"] === "vaba" ? "selected" : "" ?>>vaba</option>
                                    <option value="broneeritud" <?= $row["Status"] === "broneeritud" ? "selected" : "" ?>>broneeritud</option>
                                    <option value="hooldus" <?= $row["Status"] === "hooldus" ? "selected" : "" ?>>hooldus</option>
                                </select>
                                <button type="submit" name="update_status">Muuda staatus</button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Oled kindel, et soovid kustutada?');">
                                <input type="hidden" name="auto_id" value="<?= $row['AutoID'] ?>">
                                <button type="submit" name="delete_car">Kustuta</button>
                            </form>
                        <?php else: ?>
                            <?php if ($row["Status"] === "vaba"): ?>
                                <form method="POST">
                                    <input type="hidden" name="auto_id" value="<?= $row["AutoID"] ?>">
                                    <button type="submit" name="book_car">Broneeri</button>
                                </form>
                            <?php else: ?>
                                <p class="unavailable">Pole saadaval</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Hetkel pole saadaval ühtegi autot.</p>
    <?php endif; ?>
</div>

<script src="script.js"></script>
<footer>
    leht tegi @Andrei L
</footer>
</body>
</html>

<?php $yhendus->close(); ?>

