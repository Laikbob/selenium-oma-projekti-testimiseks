<?php if (isset($_GET['code'])){die(highlight_file(__FILE__, 1));}?>
<?php
include('zoneconf.php');
session_start();


$error = ""; // переменная для вывода ошибки

if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    global $yhendus;

    $login = htmlspecialchars(trim($_POST['login']));
    $pass = htmlspecialchars(trim($_POST['pass']));

    $sool = 'cool';
    $krypt = crypt($pass, $sool);

    $paring = $yhendus->prepare("SELECT kasutaja, parool, onadmin FROM kasutajad WHERE kasutaja=? AND parool=?");
    $paring->bind_param('ss', $login, $krypt);
    $paring->bind_result($kasutaja, $parool, $onadmin);
    $paring->execute();

    $_SESSION['onadmin'] = $onadmin;


    if ($paring->fetch() && $parool == $krypt) {
        $_SESSION['kasutaja'] = $login;
        $_SESSION['admin'] = ($onadmin == 1);
        $_SESSION['onadmin'] = $onadmin; // ← добавьте ЭТУ строку
        $yhendus->close();
        header('Location: index.php');
        exit();
    } else {
        $error = "Kasutaja või parool on vale";
        $yhendus->close();
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="logirent.css">
</head>
<body>

<div class="container">
    <h1>Login Autorent</h1>

    <?php if (!empty($error)): ?>
        <p style="color: #ff4c4c; font-weight: bold;"><?= $error ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <table>
            <tr>
                <td>Login</td>
                <td><input type="text" id="login" name="login" required></td>
            </tr>
            <tr>
                <td><label for="login">Salasõna</label></td>
                <td><input type="password" name="pass" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Logi sisse</button>
                    <a href="script.php" class="btn-link">Registreeru</a>
                </td>
            </tr>
        </table>
    </form>
</div>

<footer>
    leht tegi @Andrei L
</footer>

</body>
</html>
