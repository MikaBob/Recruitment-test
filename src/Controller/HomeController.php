<?php

namespace Blexr\Controller;

class HomeController extends DefaultController {

    public function index() {
        echo $this->twig->render('Home/index.html.twig', ['title' => 'Home']);
    }

}
