<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$file = '../data/reservations.json';
$reservations = [];

if (file_exists($file)) {
    $reservations = json_decode(file_get_contents($file), true);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des réservations</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Gestion des réservations</h2>

<table border="1">
<tr>
    <th>Titre</th>
    <th>Date</th>
    <th>Message</th>
    <th>Statut</th>
    <th>Actions</th>
</tr>
<?php foreach ($reservations as $r): ?>
<tr>
    <td><?php echo htmlspecialchars($r['title']); ?></td>
    <td><?php echo htmlspecialchars($r['date']); ?></td>
    <td><?php echo htmlspecialchars($r['message']); ?></td>
    <td><?php echo htmlspecialchars($r['status']); ?></td>
    <td>
        <?php if ($r['status'] == 'pending'): ?>
            <a href="validate-reservations.php?id=<?php echo $r['id']; ?>&action=accept">Accepter</a>
            <a href="validate-reservations.php?id=<?php echo $r['id']; ?>&action=reject">Rejeter</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<a href="dashboard-admin.php">Retour au Dashboard</a>
</body>
</html>