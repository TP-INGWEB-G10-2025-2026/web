<?php
session_start();

if (!isset($_SESSION['teacher'])) {
    header("Location: ../auth/login-teacher.php");
    exit;
}

$file = '../data/reservations.json';

$reservations = [];

if (file_exists($file)) {
    $reservations = json_decode(file_get_contents($file), true);
}

$email = $_SESSION['teacher_email'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes réservations</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav>
    <a href="dashboard-enseignant.php">Dashboard</a>
    <a href="reservations.php">Nouvelle réservation</a>
    <a href="../login/logout.php">Déconnexion</a>
</nav>

<h2>Mes réservations</h2>

<table>

<tr>
    <th>Titre</th>
    <th>Date</th>
    <th>Message</th>
    <th>Statut</th>
</tr>

<?php foreach($reservations as $r): ?>

<?php if($r['email'] == $email): ?>

<tr>

<td><?= htmlspecialchars($r['title']) ?></td>
<td><?= htmlspecialchars($r['date']) ?></td>
<td><?= htmlspecialchars($r['message']) ?></td>

<td>

<?php
if($r['status'] == "pending"){
    echo "⏳ En attente";
}
elseif($r['status'] == "accepted"){
    echo "✅ Acceptée";
}
else{
    echo "❌ Refusée";
}
?>

</td>

</tr>

<?php endif; ?>

<?php endforeach; ?>

</table>

</body>
</html>