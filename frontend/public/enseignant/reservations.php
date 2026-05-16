<?php
session_start();

if (!isset($_SESSION['teacher'])) {
    header("Location: ../login/login-enseignant.php");
    exit;
}

$success = false;

if (isset($_POST['save'])) {

    $file = '../data/reservations.json';

    $reservation = [
        "id" => time(),
        "teacher" => $_SESSION['teacher'],
        "email" => $_SESSION['teacher_email'],
        "title" => $_POST['title'],
        "date" => $_POST['date'],
        "message" => $_POST['message'],
        "status" => "pending"
    ];

    $data = [];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    }

    $data[] = $reservation;

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle réservation</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav>
    <a href="dashboard-enseignant.php">Dashboard</a>
    <a href="my-reservations.php">Mes réservations</a>
    <a href="../login/logout.php">Déconnexion</a>
</nav>

<h2>Faire une réservation</h2>

<?php if($success): ?>
<p style="color:green;font-weight:bold;">
Réservation envoyée avec succès
</p>
<?php endif; ?>

<form method="POST">

    <input type="text"
           name="title"
           placeholder="Titre réservation"
           required>

    <input type="date"
           name="date"
           required>

    <textarea name="message"
              placeholder="Description"
              required></textarea>

    <button type="submit" name="save">
        Envoyer
    </button>

</form>

</body>
</html>