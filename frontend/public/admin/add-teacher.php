<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$success = false;
$file = 'teachers.json';

if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($password)) {
        $teacher = [
            'id' => time(),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password
        ];
        
        $teachers = [];
        if (file_exists($file)) {
            $teachers = json_decode(file_get_contents($file), true);
        }
        
        $teachers[] = $teacher;
        file_put_contents($file, json_encode($teachers, JSON_PRETTY_PRINT));
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un enseignant</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
<h2>Ajouter un enseignant</h2>
<?php if ($success) echo "<p style='color: green; font-weight: bold;'>Enseignant ajouté avec succès !</p>"; ?>
<form method="POST">
    <input type="text" name="name" placeholder="Nom complet" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Téléphone" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit" name="save">Enregistrer</button>
</form>
<a href="dashboard-admin.php">Retour au Dashboard</a>
</body>
</html>