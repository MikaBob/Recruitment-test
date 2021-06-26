<?php

namespace Blexr;

class Router {

    public static function handleRequest() {
        $request = self::parseUri();
        if (!empty($request)) {
            $controllerName = $request['controller'];
            $fullQualifiedClassName = self::getControllerFullQualifiedName($controllerName);
            $action = $request['action'];
            $params = $request['params'];

            // will call autoloader if class not already loaded.
            if (class_exists($fullQualifiedClassName, true)) {
                if (method_exists($fullQualifiedClassName, $action)) {

                    $controller = new $fullQualifiedClassName();
                    echo call_user_func([$controller, $action], $params);
                } else {
                    echo "Page not found 404 : Method $action not found in controller $controllerName";
                }
            } else {
                echo "Page not found 404 : Controller $controllerName not found";
            }
        } else {
            // Default page
            echo call_user_func(array('Blexr\\Controller\\HomeController', 'index'));
        }
    }

    private static function parseUri() {
        $path = explode('/', filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_SPECIAL_CHARS));
        $params = [];

        if (count($path) > 3) {
            for ($i = 0; $i < count($path); $i++) {
                $params[] = $path[$i];
            }
        }

        $request = [
            'controller' => empty($path[1]) ? 'Home' : ucfirst($path[1]),
            'action' => empty($path[2]) ? 'index' : $path[2],
            'params' => $params
        ];

        return $request;
    }

    private static function getControllerFullQualifiedName($controllerName) {
        return "Blexr\\Controller\\{$controllerName}Controller";
    }

    public static function generateUrl($controllerName, $action) {
        $fullQualifiedClassName = self::getControllerFullQualifiedName($controllerName);
        if (class_exists($fullQualifiedClassName, true) && method_exists($fullQualifiedClassName, $action)) {
            return 'http://' . filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_VALIDATE_DOMAIN) . "/{$controllerName}/$action";
        }
        return null;
    }

}
