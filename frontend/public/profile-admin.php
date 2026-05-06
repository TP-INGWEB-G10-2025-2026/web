<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Administrateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Profil Administrateur</h2>
<form>
    <input type="text" value="Administrateur">
    <input type="email" value="admin@gmail.com">
    <input type="password" value="admin123">
    <button>Modifier</button>
</form>
<a href="dashboard-admin.php">Retour au Dashboard</a>
</body>
</html>