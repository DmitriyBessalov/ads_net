<?php

if ($_SERVER['REQUEST_URI']=='/login'){
    $GLOBALS['role']='none';
}else{
    UsersController::getUser();
};


switch ($GLOBALS['role']) {
    case 'admin':
        return array(
            //Финансы
            'finance' => 'fin/admin',

            //Вывод и добавление площадок
            'platforms' => 'ploshadki/index',
            'platforms-add' => 'ploshadki/add',
            'platforms-edit' => 'ploshadki/edit',
            'platforms-update' => 'ploshadki/update',
            'platforms-otchicleniay' => 'ploshadki/otchicleniay',
            'platforms-spisanie' => 'ploshadki/spisanie',
            'platform-stat' => 'ploshadki/Stat',
            'platforms-del' => 'ploshadki/del',

            //Сохранение стилей виджетов
            'widget-update' => 'widgetcss/update',
            'widget-aktiv' => 'widgetcss/aktiv',

            //Статьи
            'articles' => 'article/index',
            'article-edit' => 'article/edit',
            'article-stat' => 'article/stat',
            'article-update' => 'article/update',
            'article-del' => 'article/del',
            'article-start' => 'article/start',
            'article-stop' => 'article/stop',

            //Клики
            'clicks' => 'click/index',
			
			//Уведомления
            'notifications' => 'notifications/index',
			
            //Тикеты
            'tickets' => 'ticket/index',
            'ticket' => 'ticket/index',

            //Пользователи
            'users' => 'users/index',
            'user-edit' => 'users/edit',
            'user-enter' => 'users/enter',
            'user-del' => 'users/del',

            //Авторизация
            'logout' => 'users/logout',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );break;
    case 'platform':
        return array(
            //Финансы
            'finance' => 'fin/platform',

            //Авторизация
            'logout' => 'users/logout',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );break;
    case 'advertiser':
        return array(
            //Статьи
            'articles' => 'article/index',
            'article-edit' => 'article/edit',
            'article-stat' => 'article/stat',
            'article-update' => 'article/update',
            'article-del' => 'article/del',
            'article-start' => 'article/start',
            'article-stop' => 'article/stop',

            //Авторизация
            'logout' => 'users/logout',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );
    case 'none':
        return array(
            //Авторизация
            'login' => 'users/login',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );
}