<?php
session_start();

// Zniszczenie sesji
session_unset(); // Usunięcie zmiennych sesyjnych
session_destroy(); // Zniszczenie sesji

// Przekierowanie na stronę logowania
header('Location: login_form.php'); // lub możesz ustawić na login.php
exit;
?>
