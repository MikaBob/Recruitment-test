<?php

namespace Blexr\Controller;

use Blexr\Model\Entity\User;
use Blexr\Model\UserDAO;
use Blexr\Router;

class UserAPIController {

    public function get($params) {

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "GET") {
            return json_encode(['status' => 400, 'error' => 'Bad Request']);
        }

        $id = $params[0];

        header("Content-Type: application/json; charset=UTF-8");

        $userDAO = new UserDAO();
        $user = $userDAO->getById($id);

        // Do not show users's password
        $user->setPassword('');

        if ($user === null) {
            return json_encode(['status' => 404, 'error' => 'User not found']);
        }

        return json_encode(['status' => 200, 'user' => $user]);
    }

    public function post($params) {

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "POST") {
            return json_encode(['status' => 400, 'error' => 'Bad Request']);
        }

        $id = $params[0];

        header("Content-Type: application/json; charset=UTF-8");

        $userDAO = new UserDAO();
        $user = $userDAO->getById($id);

        if ($user === null) {
            return json_encode(['status' => 404, 'error' => 'User not found']);
        }

        /**
         * @TODO make real validation with constraints
         */
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $dynamicFieldsFromForm = filter_input(INPUT_POST, 'dynamicFields', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

        $isMOLGranted = $dynamicFieldsFromForm[User::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE] === 'true' ? true : false;
        $isEAGGranted = $dynamicFieldsFromForm[User::DYNAMIC_FIELD_EMAIL_ACCESS_GRANTED] === 'true' ? true : false;
        $isGRGGranted = $dynamicFieldsFromForm[User::DYNAMIC_FIELD_GIT_REPOSITORY_GRANTED] === 'true' ? true : false;
        $isJAGGranted = $dynamicFieldsFromForm[User::DYNAMIC_FIELD_JIRA_ACCESS_GRANTED] === 'true' ? true : false;

        try {
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setDynamicFieldMicrosoftOffice($isMOLGranted);
            $user->setDynamicFieldEmailAccess($isEAGGranted);
            $user->setDynamicFieldGitRepository($isGRGGranted);
            $user->setDynamicFieldJira($isJAGGranted);

            $userDAO->update($user);
        } catch (\TypeError $error) {
            $errors[] = $error->getMessage();
        }

        // Do not show users's password
        $user->setPassword('');

        return json_encode(['status' => 200, 'user' => $user]);
    }

}
