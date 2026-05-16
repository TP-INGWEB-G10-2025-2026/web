<?php
session_start();
if (!isset($_SESSION['teacher'])) {
    header("Location: ../index.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traiter la mise à jour du profil
    $message = "Profil mis à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Enseignant</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Profil Enseignant</h2>
<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
<form method="post">
    <label>Nom: <input type="text" name="name" value="Enseignant"></label><br>
    <label>Email: <input type="email" name="email" value="teacher@gmail.com"></label><br>
    <label>Mot de passe: <input type="password" name="password" value="teacher123"></label><br>
    <button type="submit">Modifier</button>
</form>
<a href="dashboard-enseignant.php">Retour au Dashboard</a>
</body>
</html>