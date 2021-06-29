<?php

namespace Blexr\Controller;

abstract class DefaultAPIController {

    protected function generateResponse($httpCode, $message) {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($httpCode);
        return json_encode($message);
    }

}
