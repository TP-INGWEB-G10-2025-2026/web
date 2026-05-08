<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$file = 'teachers.json';
$teacher = null;
$success = false;

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: teachers-list.php");
    exit;
}

if (file_exists($file)) {
    $teachers = json_decode(file_get_contents($file), true);
    foreach ($teachers as $t) {
        if ($t['id'] == $id) {
            $teacher = $t;
            break;
        }
    }
}

if (!$teacher) {
    header("Location: teachers-list.php");
    exit;
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    if (!empty($name) && !empty($email) && !empty($phone)) {
        $teachers = json_decode(file_get_contents($file), true);
        foreach ($teachers as &$t) {
            if ($t['id'] == $id) {
                $t['name'] = $name;
                $t['email'] = $email;
                $t['phone'] = $phone;
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
    <title>Modifier enseignant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Modifier enseignant</h2>
<?php if ($success) echo "<p style='color: green; font-weight: bold;'>Enseignant modifié avec succès !</p>"; ?>
<form method="POST">
    <input type="text" name="name" value="<?php echo $teacher['name']; ?>" required>
    <input type="email" name="email" value="<?php echo $teacher['email']; ?>" required>
    <input type="text" name="phone" value="<?php echo $teacher['phone']; ?>" required>
    <button type="submit" name="update">Mettre à jour</button>
</form>
<a href="teachers-list.php">Retour à la liste</a>
</body>
</html>