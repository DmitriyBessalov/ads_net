<?php
if(!empty($_POST['role'] )){
    if(!empty($_POST['name'] )){
        if(!empty($_POST['email'] )){
            $to = "orders@corton.io";
            $subject = "Corton contact form";
            $message = '
 <h3>Получен запрос на добавление пользователя</h3></br>
 <b>Роль: </b>'.$_POST['role'].'</br>
 <b>Имя: </b>'.$_POST['name'].'</br>
 <b>Еmail: </b>'.$_POST['email'].'</br>
 <b>Сообщение: </b>'.$_POST['soobchenie'].'</br>
 <b>IP: </b>'.$_SERVER['REMOTE_ADDR'].'</br>';

            $headers  = "Content-type: text/html; charset=UTF-8 \r\n";
            $headers .= "From: <support@corton.io>\r\n";
            $result = mail($to, $subject, $message, $headers);
            if ($result){
                echo "<p class=\"textgreen\">Спасибо! Мы скоро с вами свяжемся.</p>";
            }
            else{
			echo "<p class=\"textred\">Cообщение не отправленно. Пожалуйста, попрбуйте еще раз</p>";
            }
        }else{
            echo "<p class=\"textred\">Обязательные поля не заполнены. Введите email пользователя</p>"; 
        }
    }else{
        echo "<p class=\"textred\">Обязательные поля не заполнены. Введите имя пользователя</p>";
    }
}else{
    echo "<p class=\"textred\">Обязательные поля не заполнены. Введите роль пользователя</p>";
}

