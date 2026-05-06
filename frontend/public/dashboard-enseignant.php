<?php
session_start();
if (!isset($_SESSION['teacher'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Enseignant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="profile-enseignant.php">Mon profil</a>
    <a href="logout.php">Déconnexion</a>
</nav>
<div class="container">
    <h1>Dashboard Enseignant</h1>
</div>
</body>
</html>