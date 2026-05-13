<?php
function isLogged() {
    return isset($_SESSION['utente']);
}

function isAdmin() {
    return isset($_SESSION['utente']) && $_SESSION['utente']['ruolo'] === 'admin';
}
?>