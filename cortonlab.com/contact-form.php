<?php
$_POST = array_map('addslashes', $_POST);

if(!empty($_POST['email'] )) {
    $to = "dmitriy@bessalov.ru";
    $subject = "Corton лендинг форма";
    $message = '
         <h3>Получен запрос на добавление '.$_POST['type'].'</h3></br>
         <b>Еmail: </b>'.$_POST['email'].'</br>
         <b>IP: </b>'.$_SERVER['REMOTE_ADDR'].'</br>';
    $headers  = "Content-type: text/html; charset=UTF-8 \r\nFrom: <dmitriy@bessalov.ru>\r\n";
    $result = mail($to, $subject, $message, $headers);
    if (!$result){
        echo "<p class=\"textred\">Системная ошибка</p>";
    }else{
        echo "<p class=\"textred\" style='color: #008a00'>Запрос отправлен. Спасибо.</p>";
    }
}else{
    echo "<p class=\"textred\">Email не введён</p>";
}