<?php

namespace Blexr\Controller;

// Mother class for API controller which generate json encoded response
abstract class DefaultAPIController {

    // API generic response; JSON encoded (prepare your serializer !)
    protected function generateResponse($httpCode, $message) {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($httpCode);
        return json_encode($message);
    }

}
