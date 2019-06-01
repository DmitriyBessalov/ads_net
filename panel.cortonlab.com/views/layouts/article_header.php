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
   <a href="/article-stat?id='.$_GET['id'].'" class="btnarticle">Расширенная статистика</a><button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"> <i class="icon-caret-down" style="font-size: 0.65em;"></i> </button>
</div>
';
