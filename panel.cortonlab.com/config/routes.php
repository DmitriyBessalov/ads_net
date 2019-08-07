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
            'finance-popolnenie' => 'fin/popolnenie',
            'finance-spisanie' => 'fin/spisanie',

            //Вывод и добавление площадок
            'platforms' => 'ploshadki/index',
            'platforms-add' => 'ploshadki/add',
            'platforms-edit' => 'ploshadki/edit',
            'platforms-update' => 'ploshadki/update',
            'platforms-otchicleniay' => 'ploshadki/otchicleniay',
            'platform-stat' => 'ploshadki/stat',
            'platforms-del' => 'ploshadki/del',
            'platforms-get-podcategoriya' => 'ploshadki/get_podcategoriya',
            //Сохранение стилей виджетов
            'widget-update' => 'widgetcss/update',
            'widget-aktiv' => 'widgetcss/aktiv',

            //Статьи
            'articles' => 'article/index',
            'article-edit' => 'article/edit',//Убрать из проекта
            'article-a/b' => 'article/a_b',
			'article-edit-content' => 'article/content',
			'article-edit-anons' => 'article/anons',
            'article-edit-target' => 'article/target',
            'article-edit-form' => 'article/promo_form',
            'article-stat-url' => 'article/stat_url',
            'article-stat' => 'article/stat',
            'article-update' => 'article/update',
            'article-del' => 'article/del',
            'article-clone' => 'article/clone',
            'article-start' => 'article/start',
            'article-stop' => 'article/stop',
            'article-start-all' => 'article/start_all',
            'article-stop-all' => 'article/stop_all',
            'article-anons-stop' => 'article/anons_stop',
            'article-anons-start' => 'article/anons_start',
            'article-link' => 'article/article_link',

            //Клики
            'clicks' => 'click/index',

			//Слова
            'words' => 'words/index',

            //Уведомления
            'notifications' => 'notifications/index',
            'notifikations-obrabotano' => 'notifications/obrabotano',
            'notifikations-del' => 'notifications/del',

            //Нагрузка
            'load' => 'load/index',

            //Тикеты
            //'tickets' => 'ticket/index',
            //'ticket' => 'ticket/index',

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
            'finance-vivod' => 'fin/requestcash',
			'balance' => 'fin/platformbalans',

            //Авторизация
            'logout' => 'users/logout',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );break;
    case 'copywriter':
        return array(
            //Статьи
            'articles' => 'article/index',
            'article-a/b' => 'article/a_b',
            'article-edit' => 'article/edit',//Убрать из проекта
            'article-edit-content' => 'article/content',
            'article-edit-anons' => 'article/anons',
            'article-edit-target' => 'article/target',
            'article-edit-form' => 'article/promo_form',
            'article-stat-url' => 'article/stat_url',
            'article-stat' => 'article/stat',
            'article-update' => 'article/update',
            'article-clone' => 'article/clone',
            'article-del' => 'article/del',
            'article-start' => 'article/start',
            'article-stop' => 'article/stop',
            'article-start-all' => 'article/start_all',
            'article-stop-all' => 'article/stop_all',
            'article-anons-stop' => 'article/anons_stop',
            'article-anons-start' => 'article/anons_start',
            'article-link' => 'article/article_link',

            //Слова
            'words' => 'words/index',

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
            'article-edit-content' => 'article/content',
            'article-edit-anons' => 'article/anons',
            'article-stat' => 'article/stat',
            'article-link' => 'article/article_link',

            //Авторизация
            'logout' => 'users/logout',

            //Страницы сайта
            '.+' => 'site/all',
            '' => 'site/loginform',
        );break;
    case 'manager':
        return array(
            //Вывод и добавление площадок
            'platforms' => 'ploshadki/index',
            'platforms-add' => 'ploshadki/add',
            'platforms-edit' => 'ploshadki/edit',
            'platforms-update' => 'ploshadki/update',
            'platform-stat' => 'ploshadki/stat',

            //Пользователи
            'users' => 'users/index',
            'user-edit' => 'users/edit',

            //Авторизация
            'logout' => 'users/logout',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );break;
    case 'none':
        return array(
            //Авторизация
            'login' => 'users/login',

            //Страницы сайта:
            '.+' => 'site/all',
            '' => 'site/loginform',
        );
}
