<?php
session_start();


require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



$host = "localhost";
$db = "verdefuturo";
$user = "root";
$pass = "";
$charset = "utf8mb4";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=$charset",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Errore connessione database");
}

$messaggio = "";



if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST['nome']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    if (empty($nome) || empty($email) || empty($password)) {

        $messaggio = "Compila tutti i campi.";

    } else {

   
        $check = $pdo->prepare("SELECT id FROM utenti WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {

            $messaggio = "Email già registrata.";

        } else {

           
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // INSERT UTENTE
            $stmt = $pdo->prepare("
                INSERT INTO utenti (nome, email, password, ruolo)
                VALUES (?, ?, ?, 'utente')
            ");

            $stmt->execute([$nome, $email, $hash]);

      

            $_SESSION['utente'] = [
                'nome' => $nome,
                'email' => $email,
                'ruolo' => 'utente'
            ];

        

            $mail = new PHPMailer(true);

            try {
                

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;

             
                $mail->Username = 'orecchinifederico4@gmail.com';
                $mail->Password = 'htea tpbc mjzs zfjz';

                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('orecchinifederico4@gmail.com', 'Verde Futuro');
                $mail->addAddress($email, $nome);

                $mail->isHTML(true);
                $mail->Subject = 'Benvenuto su Verde Futuro';

                
    $mail->Body = "
        <div style='font-family:Arial; text-align:center'>
            <h2>Benvenuto $nome </h2>

            <p>
                Grazie per esserti registrato a <b>Verde Futuro</b> 
            </p>

            <p style='font-size:18px; color:#2e7d32'>
                 Hai sbloccato il tuo bonus di benvenuto!
            </p>

            <p>
                Controlla il tuo account per maggiori dettagli.
            </p>

            <hr>

            <small>Verde Futuro - Insieme per un pianeta migliore </small>
        </div>
    ";

  
    $mail->send();

} catch (Exception $e) {
}

      

            header("Location: contatti.php?registrazione=ok");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Verde Futuro</h1>
</header>

<nav>
    <a href="index.html">Home</a>
    <a href="login.php">Login</a>
    <a href="register.php">Registrati</a>
</nav>

<main class="container">

<section class="card">

<h2>Registrazione</h2>

<?php if (!empty($messaggio)): ?>
    <div class="error-box">
        <?= htmlspecialchars($messaggio) ?>
    </div>
<?php endif; ?>

<form method="POST" class="form">

    <label>Nome</label>
    <input type="text" name="nome" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <input type="submit" value="Registrati">

</form>

<p class="auth-link">
    Hai già un account?
    <a href="login.php">Accedi</a>
</p>

</section>

</main>

</body>
</html>
