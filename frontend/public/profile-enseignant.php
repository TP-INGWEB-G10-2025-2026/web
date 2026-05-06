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
    <title>Profil Enseignant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Profil Enseignant</h2>
<form>
    <input type="text" value="Enseignant">
    <input type="email" value="teacher@gmail.com">
    <input type="password" value="teacher123">
    <button>Modifier</button>
</form>
<a href="dashboard-enseignant.php">Retour au Dashboard</a>
</body>
</html>