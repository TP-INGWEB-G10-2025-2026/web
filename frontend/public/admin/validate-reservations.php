<?php

$file = '../data/reservations.json';

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