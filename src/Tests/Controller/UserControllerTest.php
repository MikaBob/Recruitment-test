<?php

declare(strict_types=1);

namespace Blexr\Test\Controller;

use Blexr\Router;
use Blexr\Model\UserDAO;
use PHPUnit\Framework\TestCase;
use GuzzleHttp;

class UserControllerTest extends TestCase {

    protected $client;
    protected $cookieWithValidToken;
    protected $userDAO;

    protected function setUp(): void {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://' . $_ENV['BASE_URL']
        ]);

        $response = $this->client->post(Router::generateUrl('Authentication', 'login'), ['form_params' => ['username' => 'admin@blexr.com', 'password' => 'admin']]);

        $token = json_decode($response->getBody()->getContents())->token;
        $this->cookieWithValidToken = \GuzzleHttp\Cookie\CookieJar::fromArray(['token' => $token], $_ENV['BASE_URL']);

        $this->userDAO = new UserDAO();
    }

    public function testIndexWithoutToken() {
        $response = $this->client->get(Router::generateUrl('user', 'index', 1), []);
        $this->assertStringContainsString('You know the drill, if you want to get in, you need to log in ;)', $response->getBody()->getContents());
    }

}
