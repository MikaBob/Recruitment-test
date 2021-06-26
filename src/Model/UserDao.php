<?php

namespace Blexr\Model;

use Blexr\Model\Entity\User;

class UserDAO extends AbstractDAO {

    protected $tableName = 'user';

    public function insert(User $user) {
        $query = $this->dbConnection->prepare(""
                . "INSERT INTO $this->tableName (firstName, lastName, email, creationDate, password) "
                . "VALUES (:firstName, :lastName, :email, :creationDate, :password)");

        $query->execute([
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':email' => $user->getEmail(),
            ':creationDate' => $user->getCreationDate()->format('c'),
            ':password' => $user->getPassword()
        ]);

        return $query->fetchAll();
    }

}
