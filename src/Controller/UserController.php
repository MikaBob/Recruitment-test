<?php

namespace Blexr\Controller;

use Blexr\Entity\User;

class UserController {

    public function index() {
        $user = new User();

        return $user->getName();
    }

}
