<?php
include PANELDIR.'/views/layouts/header.php';

echo '
<div class="btncontrolarticle">
   <a href="/article-a/b?id='.$_GET['id'].'" class="btnarticlegr">A/B анализ</a>
   <a href="/article-edit-content?id='.$_GET['id'].'" class="btnarticlegr">Контент статей</a>
   <a href="/article-edit-anons?id='.$_GET['id'].'" class="btnarticlegr">Редактировать анонсы</a>
   <a href="/article-edit-target?id='.$_GET['id'].'" class="btnarticlegr">Таргетинги</a>
   <a href="/article-stat-url?id='.$_GET['id'].'" class="btnarticlegr">Анализ ссылок</a>
   <a href="/article-edit-form?id='.$_GET['id'].'" class="btnarticlegr">Форма заказа</a>
   <a href="/article-stat?id='.$_GET['id'].'" class="btnarticle" style="border-radius: 4px 0 0 4px;">Расширенная статистика</a>
   <div class="dropdown">
      <button onclick="myFunction()" class="dropbtn"><i class="fa fa-caret-down"></i></button>
   <div id="myDropdown" class="dropdown-content">
     <a href="#home">Home</a>
     <a href="#about">About</a>
     <a href="#contact">Contact</a>
   </div>
</div>
</div>
';
