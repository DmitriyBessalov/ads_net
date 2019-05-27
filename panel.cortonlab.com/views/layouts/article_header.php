<?php
include PANELDIR.'/views/layouts/header.php';

echo '
<div class="btncontrolarticle">
   <a href="article-edit?id='.$_GET['id'].'" class="btnarticlegr">Старая версия</a>
   <a href="/article-a/b?id='.$_GET['id'].'" class="btnarticlegr">A/B анализ</a>
   <a href="/article-edit-content?id='.$_GET['id'].'" class="btnarticlegr">Контент статьи {%}</a>
   <a href="/article-edit-anons?id='.$_GET['id'].'" class="btnarticlegr">Редактировать анонсы</a>
   <a href="/article-edit-target?id='.$_GET['id'].'" class="btnarticlegr">Таргетинги</a>
   <a href="/article-stat-url?id='.$_GET['id'].'" class="btnarticlegr">Анализ ссылок</a>
   <a href="/article-edit-form?id='.$_GET['id'].'" class="btnarticlegr">Форма заказа</a>
   <a href="/article-stat?id='.$_GET['id'].'" class="btnarticle">Расширенная статистика</a>
</div>


';
