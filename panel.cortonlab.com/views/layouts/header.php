<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title><? echo $title; ?></title>
  <base href="https://panel.cortonlab.com">
  <link href="/css/normalize.css" rel="stylesheet" type="text/css">
  <link href="/css/webflow.css" rel="stylesheet" type="text/css">
  <link href="/css/panel-corton-io2.1.css" rel="stylesheet" type="text/css">
  <link href="/css/admin.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({google: {families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic","Inconsolata:400,700","Roboto:300,300italic,regular,italic,500,500italic,700,700italic:cyrillic,latin"]  }});</script>
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart" in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <script type="text/javascript" src="/js/tcal.js"></script>
  <script src="https://panel.cortonlab.com/js/Chart.min.js"></script>
  <script src="https://panel.cortonlab.com/js/utils.js"></script>
  <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
  <meta name="robots" content="none">
</head>
<body class="body">
  <div class="left-menu">
	  <img src="/images/cortonlab.png" alt="" class="image">
      <? if ($GLOBALS['role']=='platform'): ?>
          <a href="/finance" class="link-block w-inline-block"><img src="/images/ic-fin.png" class="image-6"><div class="text-block-82-copy">Статистика</div></a>
      <? endif; ?>
      <? if ($GLOBALS['role']=='advertiser'): ?>
          <a href="/articles?active=all" class="link-block w-inline-block"><img src="/images/ic-content.png" class="image-6"><div class="text-block-82">Статьи</div></a>
      <? endif; ?>
      <? if (($GLOBALS['role']=='manager')): ?>
          <a href="/platforms?status=1" class="link-block w-inline-block"><img src="/images/ic-platform.png" class="image-6"><div class="text-block-82-copy">Площадки</div></a>
          <a href="/users" class="link-block w-inline-block"><img src="/images/ic-user.png" class="image-6"><div class="text-block-82">Пользователи</div></a>
      <? endif; ?>
      <? if ($GLOBALS['role']=='admin'): ?>
	        <a href="/finance" class="link-block w-inline-block"><img src="/images/ic-fin.png" class="image-6"><div class="text-block-82-copy">Финансы</div></a>
	        <a href="/platforms?status=1" class="link-block w-inline-block"><img src="/images/ic-platform.png" class="image-6"><div class="text-block-82-copy">Площадки</div></a>
            <a href="/articles?active=1" class="link-block w-inline-block"><img src="/images/ic-content.png" class="image-6"><div class="text-block-82">Статьи</div></a>
            <a href="/clicks" class="link-block w-inline-block"><img src="/images/ic-click.png" class="image-6"><div class="text-block-82">Клики</div></a>
			<a href="/notifications" class="link-block w-inline-block"><img src="/images/ic-notice.png" class="image-6"><div class="text-block-82">Уведомления</div>
                <?

                $sql = "SELECT COUNT(*) FROM `notifications` WHERE `status`='0'";
                $notification = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                if ($notification) echo '<div class="circlnotice" >'.$notification.'</div></a>';
                ?>
            <a href="/users" class="link-block w-inline-block"><img src="/images/ic-user.png" class="image-6"><div class="text-block-82">Пользователи</div></a>
      <? endif; ?>
            <!--a href="/tickets" class="link-block w-inline-block"><img src="/images/ic-ticket.png" class="image-6"><div class="text-block-82">Тикеты</div></a-->
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
                        $string = $GLOBALS['email'];
                        echo $string[0];
                        ?>
                    </div>
                </a>
                <ul class="sub-menutwo">
				    <div>
					   <span style="line-height: 35px;">
                           <? echo $GLOBALS['email']; ?>
                       </span><br>
                       <a href="https://panel.cortonlab.com/#">Настройки</a><br>
                        <? if (($GLOBALS['role']=='platform')): ?>
                        <a id="vivod">Запрос вывода средств</a><br>
                        <? endif; ?>
                       <a href="https://panel.cortonlab.com/logout">Выход</a>
					</div>
                </ul>			
            </div>
        </div>

      <div class="bodys">
	  