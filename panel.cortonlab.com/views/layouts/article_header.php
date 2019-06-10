<?php
include PANELDIR.'/views/layouts/header.php';

if (!isset($result['main_promo_id'])){
    $result['main_promo_id']=$_GET['id'];
}

echo '
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="btncontrolarticle">
   <a href="/article-edit-content?id='.$result['main_promo_id'].'" class="btnarticlegr">Контент</a>
   <a href="/article-edit-anons?id='.$result['main_promo_id'].'" class="btnarticlegr">Анонсы</a>
   <a href="/article-edit-target?id='.$result['main_promo_id'].'" class="btnarticlegr">Таргетинги</a>
   <a href="/article-edit-form?id='.$result['main_promo_id'].'" class="btnarticlegr">Форма заказа</a>
   <a href="/article-stat?id='.$result['main_promo_id'].'" class="btnarticle" style="border-radius: 4px 0 0 4px;">Cтатистика</a>
   <div class="dropdown">
      <button onclick="myFunction()" class="dropbtn"><i class="fa fa-caret-down"></i></button>
   <div id="myDropdown" class="dropdown-content">
     <a href="/article-a/b?id='.$result['main_promo_id'].'">A/B анализ</a>
     <a href="/article-stat-url?id='.$result['main_promo_id'].'">Анализ ссылок</a>
   </div>
</div>
</div>';