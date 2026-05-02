<?php
session_start();
function users(){ return json_decode(file_get_contents('users.json'), true); }
function save_users($u){ file_put_contents('users.json', json_encode($u, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); }
?>
<?php
$msg='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 foreach(users() as $u){
   if($u['email']==$_POST['email'] && $u['password']==$_POST['password'] && $u['role']==$_POST['role']){
     $_SESSION['user']=$u;
     header('Location: '.($u['role']=='admin'?'dashboard_admin.php':'dashboard_enseignant.php')); exit;
   }
 }
 $msg='Identifiants invalides';
}
?><!doctype html><html><head><link rel='stylesheet' href='style.css'></head><body><div class='container'>
<h2>Connexion</h2><form method='post'>
<input name='email' placeholder='Email' required>
<input name='password' placeholder='Mot de passe' required>
<select name='role'><option value='admin'>Admin</option><option value='enseignant'>Enseignant</option></select>
<button>Connexion</button></form><p><?= $msg ?></p></div></body></html>