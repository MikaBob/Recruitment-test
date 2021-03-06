<?php

namespace Blexr;

use Blexr\Controller\AuthenticationController;

class Router {

    /**
     * Parse Uri of type/ControllerName/ActionName/param1/param2/...
     *
     * Call controller and print its response.
     *
     * Default controller is AuthenticationController
     * Default action is index
     *
     */
    public static function handleRequest() {
        $request = self::parseUri();
        if (!empty($request)) {

            $controllerName = $request['controller'];
            $fullQualifiedClassName = self::getControllerFullQualifiedName($controllerName);
            $action = $request['action'];
            $params = $request['params'];

            // If wants to log in (use AuthenticationController) or already is logged in
            if ($fullQualifiedClassName === AuthenticationController::class || AuthenticationController::hasValidJWTToken()) {

                // class_exists()  will call the autoloader if class not already loaded.
                if (class_exists($fullQualifiedClassName, true)) {
                    if (method_exists($fullQualifiedClassName, $action)) {

                        if (AuthenticationController::hasAccess($fullQualifiedClassName, $action)) {
                            $controller = new $fullQualifiedClassName();
                            echo call_user_func([$controller, $action], $params);
                        } else {
                            http_response_code(403);
                            echo "Error 403 : Forbidden";
                        }
                    } else {
                        http_response_code(404);
                        echo "Page not found 404 : Method $action not found in controller $controllerName";
                    }
                } else {
                    http_response_code(404);
                    echo "Page not found 404 : Controller $controllerName not found";
                }
            } else {
                echo call_user_func([new AuthenticationController(), 'index']);
            }
        } else {
            echo call_user_func([new AuthenticationController(), 'index']);
        }
    }

    private static function parseUri() {
        $path = explode('/', filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_SPECIAL_CHARS));
        $params = [];

        // If there are params in the url, after the action
        if (count($path) > 3) {
            // starting from 3 to skip Controller name and Action name
            for ($i = 3; $i < count($path); $i++) {
                $params[] = $path[$i];
            }
        }


        // Default Controller and action are Authentication and index
        $request = [
            'controller' => empty($path[1]) ? 'Authentication' : ucfirst($path[1]),
            'action' => empty($path[2]) ? 'index' : $path[2],
            'params' => $params
        ];

        return $request;
    }

    private static function getControllerFullQualifiedName($controllerName) {
        return "Blexr\\Controller\\{$controllerName}Controller";
    }

    public static function generateUrl($controllerName, $action, ...$params) {
        $fullQualifiedClassName = self::getControllerFullQualifiedName($controllerName);
        if (class_exists($fullQualifiedClassName, true) && method_exists($fullQualifiedClassName, $action)) {
            $urlString = "/{$controllerName}/$action";
            foreach ($params as $param) {
                $urlString .= '/' . $param;
            }
            return $urlString;
        }
        return null;
    }

}
