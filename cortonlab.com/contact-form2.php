<?php
$_POST = array_map('addslashes', $_POST);
if(!empty($_POST['host'] )){
    if(!empty($_POST['email'] )){
        $to = "orders@cortonlab.com";
        $subject = "Corton contact form";
        $message = '

         <h3>Получен запрос на добавление площадки от зарегистрированного пользователя</h3></br>
         <b>Площадка: </b>'.$_POST['host'].'</br>
         <b>Еmail: </b>'.$_POST['email'].'</br>
         <b>IP: </b>'.$_SERVER['REMOTE_ADDR'].'</br>';

        $headers  = "Content-type: text/html; charset=UTF-8 \r\nFrom: <support@cortonlab.com>\r\n";
        $result = mail($to, $subject, $message, $headers);
    }
}