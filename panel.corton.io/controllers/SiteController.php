<?php

class SiteController
{

    public static function actionLoginform()
    {
        header('Location: https://corton.io/#openModal2');
        /*echo'<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <base href="https://panel.corton.io">
  <title>Corton - Авторизация</title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/panel-corton-io.webflow.css" rel="stylesheet" type="text/css">
  <link href="css/admin.css" rel="stylesheet" type="text/css">
  <meta name="robots" content="none">
</head>
<body class="body"><img src="images/logo-corton.png" class="image">
  <div class="w-form">
    <form method="post" action="https://panel.corton.io/login" class="form-8"><label for="login">Логин</label>
    <input type="text" class="w-input" autofocus="true" maxlength="256" name="login" data-name="login" id="login" required="">
    <label for="password">Пароль</label>
    <input type="password" class="w-input" maxlength="256" name="password" data-name="password" id="password" required="">
    <input type="submit" value="Войти" data-wait="Please wait..." class="w-button">
    </form>
    </div>
  </div>
</body>
</html>';*/
        exit;
    }

    public static function actionAll()
    {
        header("Content-Type: text/html; charset=utf-8");
        header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
        echo'<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <base href="https://panel.corton.io">
  <title>Corton</title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta http-equiv="refresh" content="3;url=https://corton.io" />
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/panel-corton-io.webflow.css" rel="stylesheet" type="text/css">
  <link href="css/admin.css" rel="stylesheet" type="text/css">
  <meta name="robots" content="none">
</head>
<body class="body"><img src="images/logo-corton.png" alt="" class="image">
  <div class="w-form"><center>
    <h1>Страница не найдена</h1>
    </center></div>
  </div>
</body>
</html>';
        return true;
    }

    public static function actionLogout()
    {
        session_destroy();
        header('Location: https://corton.io/');
        return true;
    }
}
