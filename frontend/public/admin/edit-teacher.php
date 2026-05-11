<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$success = false;
$file = 'teachers.json';
$teacher = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (file_exists($file)) {
        $teachers = json_decode(file_get_contents($file), true);
        foreach ($teachers as $t) {
            if ($t['id'] == $id) {
                $teacher = $t;
                break;
            }
        }
    }
}

if (isset($_POST['save']) && $teacher) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($password)) {
        $teachers = json_decode(file_get_contents($file), true);
        foreach ($teachers as &$t) {
            if ($t['id'] == $teacher['id']) {
                $t['name'] = $name;
                $t['email'] = $email;
                $t['phone'] = $phone;
                $t['password'] = $password;
                break;
            }
        }
        file_put_contents($file, json_encode($teachers, JSON_PRETTY_PRINT));
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un enseignant</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Modifier un enseignant</h2>
<?php if ($success) echo "<p style='color: green; font-weight: bold;'>Enseignant modifié avec succès !</p>"; ?>
<?php if ($teacher): ?>
<form method="POST">
    <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($teacher['phone']); ?>" required>
    <input type="password" name="password" value="<?php echo htmlspecialchars($teacher['password']); ?>" required>
    <button type="submit" name="save">Enregistrer</button>
</form>
<?php else: ?>
<p>Enseignant non trouvé.</p>
<?php endif; ?>
<a href="teachers-list.php">Retour à la liste</a>
</body>
</html>