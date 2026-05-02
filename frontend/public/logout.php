<?php
session_start();
function users(){ return json_decode(file_get_contents('users.json'), true); }
function save_users($u){ file_put_contents('users.json', json_encode($u, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); }
?><?php session_destroy(); header('Location: login.php');