<?php
$url=preg_replace('/\/?\?.*/', '', htmlspecialchars($_SERVER["REQUEST_URI"]));
if ($url==""){$url="/";};
$content = Content::getContent($url);

if (isset($_POST['login-form']['login'])){
    $session=substr(sha1(rand()), 0, 26);
    setcookie ( 'PHPSESSID', $session, time () + 10000000 );
    $db = Db::getConnection();
    $email=$_POST['login-form']['login'];
    $password=md5($_POST['login-form']['password']);
    $ip=$_SERVER['REMOTE_ADDR'];
    $sql="SELECT `password_md5` FROM `user` WHERE `email`='".$email."' LIMIT 1;";
    $result=$db->query($sql);
    if ($result) {
        header("Location: /login");
    }else {
        $pass = $result->fetchColumn();
        if ($pass === $password) {
            $sql = "UPDATE `user` SET `phpsession` = '" . $session . "',`last_ip`='" . $ip . "' WHERE `email`='" . $email . "';";
            $db->query($sql);
            header("Location: /panel");
            exit;
        };
    }
}elseif (isset($_POST['ContactForm']['name'])  ) {
    $_SESSION = array();
    $session=session_id();
    $db = Db::getConnection();
    $fio=$_POST['ContactForm']['name'];
    $email=$_POST['ContactForm']['email'];
    $phone=$_POST['ContactForm']['phone'];
    $url=$_POST['ContactForm']['url'];
    $ip=$_SERVER['REMOTE_ADDR'];
    $sql="INSERT INTO `user` SET `email` = `".$email."`, `fio` = `".$fio."`, `role`=`moderator`, `phone` = `".$phone."`, `phpsession` = '".$session."'`,`url` = `".$url."`, `registration_ip` = `".$ip."`, `last_ip` = `".$ip."`;";
    $db->query($sql);
    $headers = "From: admin@cortonlab.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $subject = 'Новый модератор в сервисе Cortonlab.com';
    $message = "<html><body>
                <b>Имя пользователя:</b> ".$fio."<br>
                <b>Телефон пользователя:</b> ".$phone."<br>
                <b>URL полщадки:</b> ".$url."<br>
                <a href=\"http://cotrton.io/panel\">Открыть список пользоваелей на сайте</a>
                </body></html>";
    mail('leads@cortonlab.com', $subject, $message, $headers);
} elseif (isset($_POST['ContactFormtwo']['name'])) {
    $_SESSION = array();
    $session=session_id();
    $db = Db::getConnection();
    $fio=$_POST['ContactFormtwo']['name'];
    $email=$_POST['ContactFormtwo']['email'];
    $phone=$_POST['ContactFormtwo']['phone'];
    $ip=$_SERVER['REMOTE_ADDR'];
    $sql="INSERT INTO `user` SET `email` = '".$email."', `fio` = '".$fio."', `role`='reklamodatel', `phone` = '".$phone."', `phpsession` = '".$session."', `registration_ip` = '".$ip."', `last_ip` = '".$ip."';";
    $db->query($sql);
    $headers = "From: admin@cortonlab.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $subject = 'Новый рекламодатель в сервисе Cortonlab.com';
    $message = "<html><body>
                <b>Имя пользователя:</b> ".$fio."<br>
                <b>Телефон пользователя:</b> ".$phone."<br>
                <b>URL полщадки:</b> ".$url."<br>
                <a href=\"http://cotrton.io/panel\">Открыть список пользоваелей на сайте</a>
                </body></html>";
    mail('leads@cortonlab.com', $subject, $message, $headers);
};


