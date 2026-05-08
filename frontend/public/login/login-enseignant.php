<?php
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == "teacher@gmail.com" && $password == "teacher123") {
        $_SESSION['teacher'] = "Enseignant";
        $_SESSION['role'] = "teacher";
        header("Location: dashboard-enseignant.php");
        exit;
    } else {
        $error = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Enseignant</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<h2>Connexion Enseignant</h2>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit" name="login">Connexion</button>
</form>
</body>
</html>