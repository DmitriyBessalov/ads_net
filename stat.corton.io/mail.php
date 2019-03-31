<?php
header('Access-Control-Allow-Origin: *');
$to = "orders@corton.io";
$subject = "Corton promo form";

if (htmlspecialchars($_GET['host'])==""){
    $host=$_SERVER['HTTP_HOST'];
}else{
    $host=htmlspecialchars($_GET['host']);
}
$name=htmlspecialchars($_GET['name']);
$phone=htmlspecialchars($_GET['phone']);

$message = '
 <h3>Получена новая заявка</h3> </br>
 <b>Имя: </b>'.$name.'</br>
 <b>Телефон: </b>'.$phone.'</br>
 <b>Реферер: </b>'.$host.'</br>
 <b>Страница со статьёй: </b>'.$_SERVER['HTTP_REFERER'].'</br>
 <b>IP: </b>'.$_SERVER['REMOTE_ADDR'].'</br>
';

$headers  = "Content-type: text/html; charset=UTF-8 \r\n";
$headers .= "From: <support@corton.io>\r\n";
mail($to, $subject, $message, $headers);
echo 'Спасибо! Мы скоро с вами свяжемся.';