<?php

namespace Blexr\Controller;

use Blexr\Model\UserDAO;

class AuthenticationController extends DefaultController {

    /**
     * a.k.a. Login page
     */
    public function index() {
        $error = null;

        echo $this->twig->render('Authentication/login.html.twig', ['error' => $error]);
    }

    public function login($params) {
        header("Content-Type: application/json; charset=UTF-8");

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "POST") {
            return json_encode(['status' => 400, 'error' => 'Bad Request']);
        }

        $email = filter_input(INPUT_POST, 'username', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        if ($email !== "" && $email !== false && $password !== "" && $password !== "") {
            $userDAO = new UserDAO();
            $user = $userDAO->getByEmail($email);

            if ($user !== null) {
                if (password_verify($password, $user->getPassword())) {
                    $user->setLastLogin(new \DateTime());
                    $userDAO->update($user);/** @TODO check that SQL went well */
                    // Do not show users's password
                    $user->setPassword('');
                    return json_encode(['status' => 200, 'user' => $user]);
                }
            }
        }

        return json_encode(['status' => 400, 'error' => 'Invalid credidentials']);
    }

}
