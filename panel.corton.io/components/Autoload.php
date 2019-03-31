<?php

/**
 * Функция __autoload для автоматического подключения классов
 */
function __autoload($class_name)
{
    // Массив папок, в которых могут находиться необходимые классы
    $array_paths = array(
        DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR,
        DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR,
    );

    // Проходим по массиву папок
    foreach ($array_paths as $path) {

        // Формируем имя и путь к файлу с классом
        $path = PANELDIR . $path . $class_name . '.php';

        // Если такой файл существует, подключаем его
        if (is_file($path)) {
            include_once $path;
        }
    }
}
