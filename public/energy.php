<?php
$host_name = 'www.theunderworld.mk';
$database = 'theunder_main';
$user_name = 'theunder_damch';
$password = 'TGUD=_{2(QKS';
$connect = mysqli_connect($host_name, $user_name, $password, $database);

if (mysqli_connect_errno()) {
    die('<p>Failed to connect to MySQL: '.mysqli_connect_error().'</p>');
} else {
   mysqli_query($connect,"UPDATE users_energy SET users_energy.energija =  users_energy.energija + 5 WHERE (users_energy.status = 1) AND users_energy.energija < 100");
   mysqli_close($connect);
}
?>