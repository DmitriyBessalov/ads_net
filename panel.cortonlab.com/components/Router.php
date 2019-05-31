<?php

class Router
{
    /**
     * Свойство для хранения массива роутов
     * @var array 
     */
    private $routes;

    /**
     * Конструктор
     */
    public function __construct()
    {
        // Путь к файлу с роутами
        $routesPath = PANELDIR . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'routes.php';

        // Получаем роуты из файла
        $this->routes = include($routesPath);
    }

    /**
     * Возвращает строку запроса
     */
    private function getURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    /**
     * Метод для обработки запроса
     */
    public function run()
    {
        $uri = urldecode ($this->getURI());
        $str=strpos($uri, "?");
        if($str) $uri=substr($uri, 0, $str);
        foreach ($this->routes as $uriPattern => $path) {
            if (preg_match("~$uriPattern~", $uri)) {
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);
                $segments = explode('/', $internalRoute);
                $controllerName = array_shift($segments) . 'Controller';
                $controllerName = ucfirst($controllerName);
                $actionName = 'action' . ucfirst(array_shift($segments));
                $parameters = $segments;
                $controllerFile = PANELDIR . DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    include_once($controllerFile);
                }
                $controllerObject = new $controllerName;
                $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
                if ($result != null) {
                    break;
                }
            }
        }
    }
}
