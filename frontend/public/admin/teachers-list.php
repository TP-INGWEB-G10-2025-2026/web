<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$file = 'teachers.json';
$teachers = [];

if (file_exists($file)) {
    $teachers = json_decode(file_get_contents($file), true);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des enseignants</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Liste des enseignants</h2>

<table border="1">
<tr>
    <th>Nom</th>
    <th>Email</th>
    <th>Téléphone</th>
    <th>Actions</th>
</tr>
<?php foreach ($teachers as $teacher): ?>
<tr>
    <td><?php echo $teacher['name']; ?></td>
    <td><?php echo $teacher['email']; ?></td>
    <td><?php echo $teacher['phone']; ?></td>
    <td>
        <a href="edit-teacher.php?id=<?php echo $teacher['id']; ?>">Modifier</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
<a href="dashboard-admin.php">Retour au Dashboard</a>
</body>
</html>