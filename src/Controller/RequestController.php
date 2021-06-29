<?php

namespace Blexr\Controller;

class RequestController extends DefaultController {

    public function index() {
        echo $this->twig->render('Request/index.html.twig');
    }

}
