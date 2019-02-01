<?php
$host_name = 'www.theunderworld.mk';
$database = 'theunder_main';
$user_name = 'theunder_damch';
$password = 'TGUD=_{2(QKS';
$connect = mysqli_connect($host_name, $user_name, $password, $database);

if (mysqli_connect_errno()) {
    die('<p>Failed to connect to MySQL: '.mysqli_connect_error().'</p>');
} else {
   mysqli_query($connect,"UPDATE stocks SET stocks.CP = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) )),
stocks.SV = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) )),
stocks.YK = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) )),
stocks.AH = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) )),
stocks.VL = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) )), 
stocks.BL = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) )), 
stocks.CT = FLOOR(RAND()*( (stocks.price + ((stocks.price * 10) / 100) ) - (stocks.price - ((stocks.price * 10) / 100) ) + 1) + (stocks.price - ((stocks.price * 10) / 100) ))");
   mysqli_close($connect);
}
?>