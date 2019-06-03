<?php
include PANELDIR.'/views/layouts/header.php';

echo '
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="btncontrolarticle">
   <a href="/article-edit-content?id='.$_GET['id'].'" class="btnarticlegr">Контент статей</a>
   <a href="/article-edit-anons?id='.$_GET['id'].'" class="btnarticlegr">Редактировать анонсы</a>
   <a href="/article-edit-target?id='.$_GET['id'].'" class="btnarticlegr">Таргетинги</a>
   <a href="/article-edit-form?id='.$_GET['id'].'" class="btnarticlegr">Форма заказа</a>
   <a href="/article-stat?id='.$_GET['id'].'" class="btnarticle" style="border-radius: 4px 0 0 4px;">Cтатистика</a>
   <div class="dropdown">
      <button onclick="myFunction()" class="dropbtn"><i class="fa fa-caret-down"></i></button>
   <div id="myDropdown" class="dropdown-content">
     <a href="/article-a/b?id='.$_GET['id'].'">A/B анализ</a>
     <a href="/article-stat-url?id='.$_GET['id'].'">Анализ ссылок</a>
   </div>
</div>
</div>
';
