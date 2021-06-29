<?php

namespace Blexr\Model;

use Blexr\Model\Entity\User;

class UserDAO extends AbstractDAO {

    protected $tableName = 'user';

    public function __construct() {
        parent::__construct($this->tableName);
    }

    public function insert(User $user): \PDOStatement {
        $query = $this->dbConnection->prepare(''
                . 'INSERT INTO `' . $this->tableName . '` (firstName, lastName, email, creationDate, password, dynamicFields) '
                . 'VALUES (:firstName, :lastName, :email, :creationDate, :password, :dynamicFields)');

        $query->execute([
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':email' => $user->getEmail(),
            ':creationDate' => $user->getCreationDate()->format('c'),
            ':password' => $user->getPassword(),
            ':dynamicFields' => json_encode($user->getDynamicFields())
        ]);

        return $query;
    }

    public function update(User $user): \PDOStatement {
        $query = $this->dbConnection->prepare(''
                . 'UPDATE `' . $this->tableName . '` SET firstName = :firstName, lastName = :lastName, email = :email, dynamicFields = :dynamicFields '
                . 'WHERE id = :id');

        $query->execute([
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':email' => $user->getEmail(),
            ':dynamicFields' => json_encode($user->getDynamicFields()),
            ':id' => $user->getId()
        ]);

        return $query;
    }

    public function delete(User $user) {
        $query = $this->dbConnection->prepare('DELETE FROM`' . $this->tableName . '` WHERE id = :id');

        $query->execute([
            ':id' => $user->getId()
        ]);

        return $query;
    }

    public function getById($id): ?User {
        $obj = parent::getById($id);

        if ($obj !== false) {
            return $this->fromObjToUser($obj);
        }
        return null;
    }

    public function getByEmail($email): ?User {
        if (is_string($email) && $email !== '') {
            $query = $this->dbConnection->prepare('SELECT * FROM `' . $this->tableName . '` WHERE email = :email');
            $query->execute([':email' => $email]);

            $result = $query->fetch(\PDO::FETCH_OBJ);

            if ($result !== false) {
                return $this->fromObjToUser($result);
            }
        }
        return null;
    }

    private function fromObjToUser($obj): User {
        $user = new User();
        $user->setId($obj->id);
        $user->setFirstName($obj->firstName);
        $user->setLastName($obj->lastName);
        $user->setEmail($obj->email);
        $user->setPassword($obj->password);
        $user->setCreationDate(new \DateTime($obj->creationDate));
        $user->setLastLogin($obj->lastLogin === null ? null : new \DateTime($obj->lastLogin));
        $user->setDynamicFields(json_decode($obj->dynamicFields, true));

        return $user;
    }

}
