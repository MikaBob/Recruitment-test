<?php

namespace Blexr\Controller;

use Blexr\Model\UserDAO;

class UserController extends DefaultController {

    public function index() {

        $userDAO = new UserDAO();

        $users = $userDAO->getAll();

        echo $this->twig->render('User/index.html.twig', ['users' => $users]);
    }

}
