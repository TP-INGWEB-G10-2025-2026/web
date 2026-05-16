<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traiter la mise à jour du profil
    // Ici, on pourrait mettre à jour un fichier ou une base de données
    // Pour l'exemple, on affiche un message
    $message = "Profil mis à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Administrateur</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Profil Administrateur</h2>
<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
<form method="post">
    <label>Nom: <input type="text" name="name" value="Administrateur"></label><br>
    <label>Email: <input type="email" name="email" value="admin@gmail.com"></label><br>
    <label>Mot de passe: <input type="password" name="password" value="admin123"></label><br>
    <button type="submit">Modifier</button>
</form>
<a href="dashboard-admin.php">Retour au Dashboard</a>
</body>
</html>