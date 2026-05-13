<?php
session_start();

/* elimina tutte le variabili di sessione */
$_SESSION = [];

/* distrugge la sessione */
session_destroy();

/* redirect alla pagina di login */
header("Location: login.php");
exit;