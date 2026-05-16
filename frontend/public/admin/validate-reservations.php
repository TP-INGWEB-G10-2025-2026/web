<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: reservations.php");
    exit;
}

$file = '../data/reservations.json';

if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

$data = json_decode(file_get_contents($file), true);

$id = $_GET['id'];
$action = $_GET['action'];

foreach($data as &$r){
    if($r['id'] == $id){
        if($action == "accept"){
            $r['status'] = "accepted";
        }
        if($action == "reject"){
            $r['status'] = "rejected";
        }
    }
}

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

header("Location: reservations.php");
exit;
?>