<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login-admin.php");
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
<title>Gestion réservations</title>
<link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<h2>Gestion des réservations</h2>

<table>

<tr>
    <th>Enseignant</th>
    <th>Titre</th>
    <th>Date</th>
    <th>Message</th>
    <th>Statut</th>
    <th>Action</th>
</tr>

<?php foreach($reservations as $r): ?>

<tr>

<td><?= $r['teacher'] ?></td>
<td><?= $r['title'] ?></td>
<td><?= $r['date'] ?></td>
<td><?= $r['message'] ?></td>
<td><?= $r['status'] ?></td>

<td>
    <a href="validate-reservation.php?id=<?= $r['id'] ?>&action=accept">
        Accepter
    </a>

    <a href="validate-reservation.php?id=<?= $r['id'] ?>&action=reject">
        Refuser
    </a>
</td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>