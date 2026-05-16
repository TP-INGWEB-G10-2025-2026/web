<?php
session_start();
if (!isset($_SESSION['teacher'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Enseignant</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Dashboard Enseignant</h2>
<nav>
    <a href="reservations.php">Faire réservation</a>
    <a href="my-reservations.php">Mes réservations</a>
    <a href="profile-enseignant.php">Mon profil</a>
    <a href="../login/logout.php">Déconnexion</a>
</nav>
<p>Bienvenue dans votre espace enseignant.</p>
</body>
</html>