<?php
session_start();

/* =========================
   PROTEZIONE PAGINA
========================= */
if (!isset($_SESSION['utente'])) {
    header("Location: login.php?errore=1");
    exit;
}

/* =========================
   DB CONNECTION
========================= */
$pdo = new PDO(
    "mysql:host=localhost;dbname=verdefuturo;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$errore = "";
$successo = "";
$newsletterMsg = "";

/* =========================
   CREATE UTENTE
========================= */
if (isset($_POST['create'])) {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT id FROM utenti WHERE email=?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $errore = "Email già registrata";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO utenti (nome,email,password)
            VALUES (?,?,?)
        ");

        $stmt->execute([$nome,$email,$password]);

        $successo = "Utente creato";
    }
}

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("DELETE FROM utenti WHERE id=?");
    $stmt->execute([$_GET['delete']]);

    header("Location: contatti.php");
    exit;
}

/* =========================
   UPDATE
========================= */
if (isset($_POST['update'])) {

    $stmt = $pdo->prepare("
        UPDATE utenti
        SET nome=?, email=?
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['nome'],
        $_POST['email'],
        $_POST['id']
    ]);

    header("Location: contatti.php");
    exit;
}

/* =========================
   EDIT
========================= */
$editUser = null;

if (isset($_GET['edit'])) {

    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE id=?");
    $stmt->execute([$_GET['edit']]);

    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* =========================
   READ UTENTI
========================= */
$users = $pdo->query("SELECT * FROM utenti ORDER BY id DESC")->fetchAll();

/* =========================
   NEWSLETTER + EMAIL
========================= */
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['newsletter'])) {

    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $newsletterMsg = "Email non valida.";
    } else {

        $check = $pdo->prepare("SELECT id FROM newsletter WHERE email=?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $newsletterMsg = "Sei già iscritto alla newsletter.";
        } else {

            $stmt = $pdo->prepare("INSERT INTO newsletter (email) VALUES (?)");
            $stmt->execute([$email]);

            try {

                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'orecchinifederico4@gmail.com';
                $mail->Password = 'kvsa edwy demq uomz';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('orecchinifederico4@gmail.com', 'Verde Futuro');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Iscrizione newsletter confermata';

                $mail->Body = "
                    <div style='font-family:Arial; text-align:center; padding:20px'>
                        <h2>Iscrizione confermata</h2>
                        <p>Sei stato iscritto alla newsletter di Verde Futuro.</p>
                        <p>Riceverai aggiornamenti sulle nostre attività.</p>
                        <hr>
                        <small>Verde Futuro</small>
                    </div>
                ";

                $mail->send();

            } catch (Exception $e) {
                $newsletterMsg = "Iscrizione avvenuta ma email non inviata.";
            }

            $newsletterMsg = "Iscrizione completata con successo!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Contatti</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
  <h1>Contatti</h1>
</header>

<nav>
  <a href="index.html">Home</a>
  <a href="logout.php">Logout</a>
</nav>

<main class="container">

<!-- UTENTI -->
<section class="card">

<h2>Utenti</h2>

<?php if (!empty($errore)): ?>
  <div class="error-box"><?= $errore ?></div>
<?php endif; ?>

<?php if (!empty($successo)): ?>
  <div class="success-box"><?= $successo ?></div>
<?php endif; ?>

<table>
<tr>
<th>ID</th><th>Nome</th><th>Email</th><th>Azioni</th>
</tr>

<?php foreach($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['nome']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td class="actions">
  <a class="btn-edit" href="?edit=<?= $u['id'] ?>">Modifica</a>
  <a class="btn-delete" href="?delete=<?= $u['id'] ?>">Elimina</a>
</td>
</tr>
<?php endforeach; ?>

</table>

</section>

<!-- NEWSLETTER -->
<section class="card">

<h2>Newsletter</h2>

<?php if (!empty($newsletterMsg)): ?>
  <div class="success-box">
    <?= htmlspecialchars($newsletterMsg) ?>
  </div>
<?php endif; ?>

<form method="POST" class="form">

<label>Email</label>
<input type="email" name="email" required>

<input type="submit" name="newsletter" value="Iscriviti">

</form>

</section>

</main>

</body>
</html>