<?php

namespace Blexr\Controller;

use Blexr\Model\UserDAO;
use Blexr\Model\Entity\User;

class AuthenticationController extends DefaultController {

    /**
     * a.k.a. Login page
     */
    public function index() {
        $error = null;
        echo $this->twig->render('Authentication/login.html.twig', ['error' => $error]);
    }

    /**
     * Ajax call to receive token
     */
    public function login($params) {
        header("Content-Type: application/json; charset=UTF-8");

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "POST") {
            http_response_code(400);
            return json_encode(['status' => 400, 'error' => 'Bad Request']);
        }

        $email = filter_input(INPUT_POST, 'username', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        if ($email !== "" && $email !== false && $password !== "" && $password !== "") {
            $userDAO = new UserDAO();
            $user = $userDAO->getByEmail($email);

            if ($user !== null) {
                if (password_verify($password, $user->getPassword())) {

                    /** @TODO check that SQL went well */
                    $user->setLastLogin(new \DateTime());
                    $userDAO->update($user);

                    // Do not show users's password
                    $user->setPassword('');

                    $token = $this->generateJWTToken($user);

                    $_SESSION['loggedUser'] = $user;

                    return json_encode(['status' => 200, 'token' => $token]);
                }
            }
        }

        return json_encode(['status' => 400, 'error' => 'Invalid credidentials']);
    }

    /**
     * https://en.wikipedia.org/wiki/JSON_Web_Token
     */
    private function generateJWTToken(User $user): string {
        $header = AuthenticationController::encodeBase64Url(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = AuthenticationController::encodeBase64Url(json_encode([
                    'id' => $user->getId(),
                    'iat' => $user->getLastLogin()->format('U'),
                    'exp' => (time() + 60 * 15) // 15 min
        ]));

        $signature = AuthenticationController::encodeBase64Url(hash_hmac('sha256', "{$header}.{$payload}", $_ENV['SECRET'], true));

        // Create JWT
        $jwt = "{$header}.{$payload}.{$signature}";

        return $jwt;
    }

    public static function hasValidJWTToken(): bool {
        $token = filter_input(INPUT_COOKIE, 'token', FILTER_SANITIZE_STRING);

        if (!is_string($token))
            return false;

        $parts = explode('.', $token);

        //Invalid token structure
        if (count($parts) !== 3)
            return false;

        $header = base64_decode($parts[0]);
        $payload = json_decode(base64_decode($parts[1]));
        $signature = $parts[2];

        // Token expired
        if (!isset($payload->exp) || $payload->exp - time() < 0)
            return false;

        if (!isset($payload->id))
            return false;

        $user = (new UserDAO())->getById($payload->id);
        // User not found
        if ($user === null)
            return false;

        // Do not show password
        $user->setPassword('');

        $signatureToHash = AuthenticationController::encodeBase64Url($header) . '.' . AuthenticationController::encodeBase64Url(json_encode($payload));

        // Repeat procedure and compare signature
        $expectedSignature = AuthenticationController::encodeBase64Url(hash_hmac('SHA256', $signatureToHash, $_ENV['SECRET'], true));

        if ($signature !== $expectedSignature) {
            return false;
        }

        $_SERVER['loggedUser'] = $user;

        return true;
    }

    /**
     * @TODO Make real role system and control user's access
     */
    public static function hasAccess($controller, $action) {
        switch ($controller) {
            case UserController::class :
            case UserAPIController::class :
                return AuthenticationController::isAdmin();
                break;
            default:
                return true;
        }

        // shoud never happened, but just in case
        return false;
    }

    private static function encodeBase64Url($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    public static function isAdmin() {
        return isset($_SERVER['loggedUser']) ? $_SERVER['loggedUser']->getId() === 1 : false;
    }

}
