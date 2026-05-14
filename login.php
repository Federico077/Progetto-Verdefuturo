<?php
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=verdefuturo;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$errore = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email=?");
    $stmt->execute([$email]);

    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utente && password_verify($password, $utente['password'])) {

    $_SESSION['utente'] = $utente['nome'];
    $_SESSION['ruolo'] = $utente['ruolo'];

    header("Location: contatti.php");
    exit;

}
    else {
        $errore = "Email o password errati";
    }
}

if (isset($_GET['errore'])) {
    $errore = "Devi fare login";
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Login</h1>
</header>

<nav>
    <a href="index.html">Home</a>
    <a href="register.php">Registrati</a>
</nav>

<main class="container">

<section class="card">

<h2>Accedi</h2>

<?php if ($errore): ?>
<div class="error-box"><?= $errore ?></div>
<?php endif; ?>

<form method="POST" class="form">

<label>Email</label>
<input type="email" name="email" required>

<label>Password</label>
<input type="password" name="password" required>

<input type="submit" value="Login">

</form>

</section>

</main>

</body>
</html>
