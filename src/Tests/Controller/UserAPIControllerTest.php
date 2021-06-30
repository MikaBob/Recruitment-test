<?php

declare(strict_types=1);

namespace Blexr\Test\Controller;

use Blexr\Router;
use Blexr\Model\UserDAO;
use PHPUnit\Framework\TestCase;
use GuzzleHttp;

class UserAPIControllerTest extends TestCase {

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

    public function testList() {
        $response = $this->client->get(Router::generateUrl('UserAPI', 'list'), ['cookies' => $this->cookieWithValidToken]);

        $users = json_decode($response->getBody()->getContents())->users;

        $expectedUsers = $this->userDAO->getAll();

        foreach ($expectedUsers as $expectedUser) {
            $expectedUser->password = '';
        }
        $this->assertEquals($expectedUsers, $users);
    }

    public function testListWithoutToken() {
        $response = $this->client->get(Router::generateUrl('userAPI', 'list'), []);
        $this->assertStringContainsString('You know the drill, if you want to get in, you need to log in ;)', $response->getBody()->getContents());
    }

    public function testListWrongHttpMethod() {
        $response = $this->client->post(Router::generateUrl('userAPI', 'list'), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testGet() {
        $response = $this->client->get(Router::generateUrl('UserAPI', 'get', 1), ['cookies' => $this->cookieWithValidToken]);

        $user = json_decode($response->getBody()->getContents())->user;

        $expectedUser = $this->userDAO->getById(1);
        $expectedUser->setPassword('');

        $this->assertEquals($expectedUser->getId(), $user->id);
        $this->assertEquals($expectedUser->getFirstName(), $user->firstName);
        $this->assertEquals($expectedUser->getLastName(), $user->lastName);
        $this->assertEquals($expectedUser->getEmail(), $user->email);
        $this->assertEquals($expectedUser->getPassword(), $user->password);
        $this->assertEquals($expectedUser->getCreationDate()->format('c'), (new \DateTime($user->creationDate->date))->format('c'));
        $this->assertEquals($expectedUser->getLastLogin()->format('c'), (new \DateTime($user->lastLogin->date))->format('c'));
        $this->assertEquals($expectedUser->getDynamicFields(), (array) $user->dynamicFields);
    }

    public function testGetWithoutToken() {
        $response = $this->client->get(Router::generateUrl('userAPI', 'get', 1), []);
        $this->assertStringContainsString('You know the drill, if you want to get in, you need to log in ;)', $response->getBody()->getContents());
    }

    public function testGetWrongHttpMethod() {
        $response = $this->client->post(Router::generateUrl('userAPI', 'get', 42), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testGetWrongParameters() {
        $response = $this->client->get(Router::generateUrl('userAPI', 'get', 'NaN'), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testGetUserNotFound() {
        $response = $this->client->get(Router::generateUrl('userAPI', 'get', -42), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"User not found"', $response->getBody()->getContents());
    }

    public function testPost() {
        $oldEmail = 'user@blexr.com';
        $user = $this->userDAO->getByEmail($oldEmail);

        $oldName = $user->getFirstName();
        $newEmail = 'test@test.com';
        $newName = 'something new';

        $user->setEmail($newEmail);
        $user->setFirstName($newName);

        $response = $this->client->post(Router::generateUrl('UserAPI', 'post', $user->getId()), [
            'cookies' => $this->cookieWithValidToken,
            'form_params' => [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'dynamicFields' => $user->getDynamicFields()
        ]]);

        $this->assertEquals(200, $response->getStatusCode());

        $newUser = json_decode($response->getBody()->getContents())->user;

        $this->assertEquals($newUser->email, $newEmail);
        $this->assertEquals($newUser->firstName, $newName);

        $user->setEmail($oldEmail);
        $user->setFirstName($oldName);
        $this->userDAO->update($user);
    }

    public function testPostWithoutToken() {
        $response = $this->client->post(Router::generateUrl('UserAPI', 'post', 1), []);
        $this->assertStringContainsString('You know the drill, if you want to get in, you need to log in ;)', $response->getBody()->getContents());
    }

    public function testPostWrongHttpMethod() {
        $response = $this->client->get(Router::generateUrl('UserAPI', 'post', 1), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testPostWrongParameters() {
        $response = $this->client->post(Router::generateUrl('UserAPI', 'post', 'NaN'), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testPostUserNotFound() {
        $response = $this->client->post(Router::generateUrl('UserAPI', 'post', -42), ['cookies' => $this->cookieWithValidToken, 'http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"User not found"', $response->getBody()->getContents());
    }

}
