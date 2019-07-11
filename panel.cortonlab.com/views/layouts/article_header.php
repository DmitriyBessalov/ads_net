<?php
include PANELDIR.'/views/layouts/header.php';

UsersController::blockArticle();

if (!isset($result['main_promo_id'])){
    $result['main_promo_id']=$_GET['id'];
}

if ($GLOBALS['role']=='advertiser'){
    echo '
    <style>
        #add_variat_promo, .ql-toolbar, .submit-button-6, .flipswitch, .delanons{
            display: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <div class="btncontrolarticle">
       <a href="/article-edit-content?id=' . $result['main_promo_id'] . '" class="btnarticlegr">Редактирование</a>
       <a href="/article-edit-anons?id=' . $result['main_promo_id'] . '" class="btnarticlegr">Управление анонсами</a>
       <a href="/article-stat?id=' . $result['main_promo_id'] . '" class="btnarticle" style="border-radius: 4px 0 0 4px;">Cтатистика</a>
    </div>
    ';
}else {
    echo '
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <div class="btncontrolarticle">
       <a href="/article-edit-content?id=' . $result['main_promo_id'] . '" class="btnarticlegr">Редактирование</a>
       <a href="/article-edit-anons?id=' . $result['main_promo_id'] . '" class="btnarticlegr">Управление анонсами</a>
       <a href="/article-edit-target?id=' . $result['main_promo_id'] . '" class="btnarticlegr">Таргетинги</a>
       <a href="/article-edit-form?id=' . $result['main_promo_id'] . '" class="btnarticlegr">Лид форма</a>
       <a href="/article-stat?id=' . $result['main_promo_id'] . '" class="btnarticle" style="border-radius: 4px 0 0 4px;">Cтатистика</a>
       <div class="dropdown">
          <button onclick="myFunction()" class="dropbtn"><i class="fa fa-caret-down"></i></button>
       <div id="myDropdown" class="dropdown-content">
         <a href="/article-a/b?id=' . $result['main_promo_id'] . '">A/B анализ</a>
         <a href="/article-stat-url?id=' . $result['main_promo_id'] . '">Анализ ссылок</a>
       </div>
    </div>
    </div>';
}