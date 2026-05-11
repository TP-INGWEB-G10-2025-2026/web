<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav>
    <a href="teachers-list.php">Liste des enseignants</a>
    <a href="add-teacher.php">Ajouter enseignant</a>
    <a href="profile-admin.php">Profil Admin</a>
    <a href="reservations.php">Gérer réservations</a>
    <a href="logout.php">Déconnexion</a>
</nav>
<div class="container">
    <h1>Dashboard Admin</h1>
</div>
</body>
</html>