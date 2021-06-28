<?php

declare(strict_types=1);

namespace Blexr\Test\Model;

use Blexr\Model\UserDAO;
use Blexr\Model\Entity\User;
use PHPUnit\Framework\TestCase;

class UserDAOTest extends TestCase {

    protected $userDAO;
    protected $TEST_EMAIL = 'test@test.com';

    protected function setUp(): void {
        $this->userDAO = new UserDAO();
    }

    public function testInsert() {
        $newUser = new User();

        $newUser->setFirstName('test');
        $newUser->setLastName('test');
        $newUser->setEmail($this->TEST_EMAIL);
        $newUser->setCreationDate(new \DateTime());
        $newUser->setLastLogin(new \DateTime());
        $newUser->setPassword('test');
        $newUser->setDynamicFields(['test']);


        // in case previous test failed, delete $newUser before testing
        $result = $this->userDAO->getByEmail($this->TEST_EMAIL);
        if ($result !== null) {
            $this->userDAO->delete($result);
        }


        $queryResult = $this->userDAO->insert($newUser);
        $expectedUser = $this->userDAO->getByEmail($this->TEST_EMAIL);

        $this->assertEquals($expectedUser->getFirstName(), $newUser->getFirstName());
        $this->assertEquals($expectedUser->getLastName(), $newUser->getLastName());
        $this->assertEquals($expectedUser->getEmail(), $newUser->getEmail());
        $this->assertEquals($expectedUser->getPassword(), $newUser->getPassword());
        $this->assertEquals($expectedUser->getLastLogin(), null);
        $this->assertEquals($expectedUser->getDynamicFields(), (array) $newUser->getDynamicFields());
        $this->assertEquals($queryResult->rowCount(), 1);

        return $expectedUser->getId();
    }

    public function testInsertWrongParameters() {
        $this->expectException(\TypeError::class);
        $this->userDAO->insert(-42);
    }

    /**
     * @depends testInsert
     */
    public function testUpdate($id) {

        $user = $this->userDAO->getById($id);
        $newFirstName = $user->getFirstName() === 'Update test' ? 'Test update' : 'Update test';
        $user->setFirstName($newFirstName);

        $queryResult = $this->userDAO->update($user);

        $newUser = $this->userDAO->getById($id);

        $this->assertEquals($newFirstName, $newUser->getFirstName());
        $this->assertEquals($user->getLastName(), $newUser->getLastName());
        $this->assertEquals($user->getEmail(), $newUser->getEmail());
        $this->assertEquals($queryResult->rowCount(), 1);
    }

    public function testUpdateWrongParameters() {
        $this->expectException(\TypeError::class);
        $this->userDAO->update(-42);


        $user = $this->userDAO->getByEmail($this->TEST_EMAIL);
        $user->setId(-42);

        $queryResult = $this->userDAO->update($user);
        $this->assertEquals($queryResult->rowCount(), 0);
    }

    /**
     * @depends testInsert
     */
    public function testGetById($id) {
        $user = $this->userDAO->getById($id);
        $this->assertEquals($this->TEST_EMAIL, $user->getEmail());
    }

    public function testGetByIdWithWrongParamters() {
        $this->assertNull($this->userDAO->getById(-42));
        $this->assertNull($this->userDAO->getById('NaN'));
    }

    /**
     * @depends testInsert
     */
    public function testGetByEmail() {
        $user = $this->userDAO->getByEmail($this->TEST_EMAIL);
        $this->assertEquals($this->TEST_EMAIL, $user->getEmail());
    }

    public function testGetByEmailWithWrongParamters() {
        $this->assertNull($this->userDAO->getByEmail(-42));
        $this->assertNull($this->userDAO->getByEmail('Not an Email'));
    }

    /**
     * @depends testInsert
     */
    public function testDelete($id) {
        $user = $this->userDAO->getById($id);

        $queryResult = $this->userDAO->delete($user);
        $this->assertNull($this->userDAO->getById($id));
        $this->assertEquals($queryResult->rowCount(), 1);
    }

    public function testDeleteWithWrongParameter() {
        $this->expectException(\TypeError::class);
        $this->assertNull($this->userDAO->delete(-42));
    }

}
