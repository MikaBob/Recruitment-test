<?php

namespace Blexr\Model;

use Blexr\Model\Entity\User;

class UserDAO extends AbstractDAO {

    protected $tableName = 'user';

    public function insert(User $user) {
        $query = $this->dbConnection->prepare(""
                . "INSERT INTO $this->tableName (firstName, lastName, email, creationDate, password, dynamicFields) "
                . "VALUES (:firstName, :lastName, :email, :creationDate, :password, :dynamicFields)");

        $query->execute([
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':email' => $user->getEmail(),
            ':creationDate' => $user->getCreationDate()->format('c'),
            ':password' => $user->getPassword(),
            ':dynamicFields' => json_encode($user->getDynamicFields()),
        ]);

        return $query->fetchAll();
    }

    public function update(User $user) {
        $query = $this->dbConnection->prepare(""
                . "UPDATE $this->tableName SET firstName = :firstName, lastName = :lastName, email = :email, dynamicFields = :dynamicFields "
                . "WHERE id = :id");

        $query->execute([
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':email' => $user->getEmail(),
            ':dynamicFields' => json_encode($user->getDynamicFields()),
            ':id' => $user->getId()
        ]);

        return $query->fetchAll();
    }

    public function getById($id): ?User {
        $obj = parent::getById($id);

        if ($obj !== null) {
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
        return null;
    }

}
