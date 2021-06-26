<?php

namespace Blexr\Controller;

use Blexr\Model\Entity\User;
use Blexr\Model\UserDAO;
use Blexr\Router;

class UserController extends DefaultController {

    public function index() {

        $userDAO = new UserDAO();

        $users = $userDAO->getAll();

        echo $this->twig->render('User/index.html.twig', ['users' => $users]);
    }

    public function create() {

        $user = null;
        $errors = [];

        // if form is submited
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) === "POST") {
            $user = new User();

            /**
             * @TODO make real validation with constraints
             */
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            try {
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setEmail($email);
                $user->setCreationDate(new \DateTime());

                $password = bin2hex(random_bytes(16));
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            } catch (\TypeError $error) {
                $errors[] = $error->getMessage();
            }

            $userDAO = new UserDAO();
            $userDAO->insert($user);

            /**
             * @TODO send email
             */
            header('Location: ' . Router::generateUrl('user', 'index'));
        }

        echo $this->twig->render('User/create.html.twig', [
            'user' => $user,
            'errors' => $errors
        ]);
    }

}
