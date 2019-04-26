<?php
$user= UsersController::getUserEmail();
?><!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title><? echo $title; ?></title>
  <base href="https://panel.corton.io">
  <link href="/css/normalize.css" rel="stylesheet" type="text/css">
  <link href="/css/webflow.css" rel="stylesheet" type="text/css">
  <link href="/css/panel-corton-io2.css" rel="stylesheet" type="text/css">
  <link href="/css/admin.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({google: {families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic","Inconsolata:400,700","Roboto:300,300italic,regular,italic,500,500italic,700,700italic:cyrillic,latin"]  }});</script>
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart" in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <script type="text/javascript" src="/js/tcal.js"></script>
  <meta name="robots" content="none">
</head>
<body class="body">
  <div class="left-menu">
	  <img src="/images/logo-corton.png" alt="" class="image">
      <? if ($GLOBALS['user']=='platform'): ?>
          <a href="/finance" class="link-block w-inline-block"><img src="/images/ic-fin.png" alt="" class="image-6"><div class="text-block-82-copy">Статистика</div></a>
      <? endif; ?>
      <? if ($GLOBALS['user']=='admin'): ?>
	        <a href="/finance" class="link-block w-inline-block"><img src="/images/ic-fin.png" alt="" class="image-6"><div class="text-block-82-copy">Финансы</div></a>
	        <a href="/platforms?status=1" class="link-block w-inline-block"><img src="/images/ic-platform.png" alt="" class="image-6"><div class="text-block-82-copy">Площадки</div></a>
      <? endif; ?>
      <? if (($GLOBALS['user']=='admin') or ($GLOBALS['user']=='advertiser')): ?>
            <a href="/articles?active=1" class="link-block w-inline-block"><img src="/images/ic-content.png" alt="" class="image-6"><div class="text-block-82">Статьи</div></a>
      <? endif; ?>
      <? if ($GLOBALS['user']=='admin'): ?>
            <a href="/clicks" class="link-block w-inline-block"><img src="/images/ic-click.png" alt="" class="image-6"><div class="text-block-82">Клики</div></a>
			<a href="/notifications" class="link-block w-inline-block"><img src="/images/ic-notice.png" alt="" class="image-6"><div class="text-block-82">Уведомления</div></a>
            <a href="/users" class="link-block w-inline-block"><img src="/images/ic-user.png" alt="" class="image-6"><div class="text-block-82">Пользователи</div></a>
      <? endif; ?>
            <!--a href="/tickets" class="link-block w-inline-block"><img src="/images/ic-ticket.png" alt="" class="image-6"><div class="text-block-82">Тикеты</div></a-->
  </div>
  <div class="header">
    <div class="div-block-88">
        <div class="div-block-90">
            <div class="h1" id="title2"><? echo $title; ?>
            </div>
            <div class="div-block-109">
                <div class="text-block-balans"></div>
		        <a class="main-item" href="javascript:void(0);" tabindex="1" style="font-size: 34px; text-decoration: none; color: #768093; float: right; padding-top: 28px;">
                    <div class="text-block-116">
                        <?
                        $string = $user;
                        echo $string[0];
                        ?>
                    </div>
                </a>
                <ul class="sub-menutwo">
				    <div>
					   <span style="line-height: 35px;">
                           <? echo $user; ?>
                       </span><br>
                       <a href="https://panel.corton.io/#">Настройки</a><br>
                       <a href="https://panel.corton.io/logout">Выход</a>
					</div>
                </ul>			
            </div>
        </div>

      <div class="bodys">
	  