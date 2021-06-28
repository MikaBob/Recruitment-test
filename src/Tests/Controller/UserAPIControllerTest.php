<?php

declare(strict_types=1);

namespace Blexr\Test\Controller;

use Blexr\Router;
use Blexr\Model\UserDAO;
use PHPUnit\Framework\TestCase;
use GuzzleHttp;

class UserAPIControllerTest extends TestCase {

    protected $client;
    protected $userDAO;

    protected function setUp(): void {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $_ENV['BASE_URL']
        ]);

        $this->userDAO = new UserDAO();
    }

    public function testGet() {

        $response = $this->client->get(Router::generateUrl('userAPI', 'get', 1), []);

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

    public function testGetWrongHttpMethod() {
        $response = $this->client->post(Router::generateUrl('userAPI', 'get', 42), ['http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testGetWrongParameters() {
        $response = $this->client->get(Router::generateUrl('userAPI', 'get', 'NaN'), ['http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"Bad Request"', $response->getBody()->getContents());
    }

    public function testGetUserNotFound() {
        $response = $this->client->get(Router::generateUrl('userAPI', 'get', -42), ['http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('"User not found"', $response->getBody()->getContents());
    }

}
