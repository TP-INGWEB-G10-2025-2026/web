<?php
session_start();
function users(){ return json_decode(file_get_contents('users.json'), true); }
function save_users($u){ file_put_contents('users.json', json_encode($u, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); }
?>
<?php if(!isset($_SESSION['user'])){header('Location: login.php');exit;}
$users=users(); foreach($users as $k=>$v){ if($v['id']==$_SESSION['user']['id']){$idx=$k; $user=$v;}}
if($_SERVER['REQUEST_METHOD']=='POST'){ $users[$idx]['nom']=$_POST['nom']; $users[$idx]['email']=$_POST['email']; save_users($users); $_SESSION['user']=$users[$idx]; $user=$_SESSION['user'];}
?><!doctype html><html><head><link rel='stylesheet' href='style.css'></head><body><div class='container'>
<h2>Mon Profil</h2><form method='post'><input name='nom' value='<?= $user['nom'] ?>'><input name='email' value='<?= $user['email'] ?>'><button>Modifier</button></form>
<a class='btn' href='<?= $user['role']=="admin"?"dashboard_admin.php":"dashboard_enseignant.php" ?>'>Retour</a></div></body></html>