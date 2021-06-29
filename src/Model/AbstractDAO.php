<?php

namespace Blexr\Model;

abstract class AbstractDAO {

    protected $dbConnection;
    protected $tableName;

    public function __construct(string $tableName) {
        $this->dbConnection = new \PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $this->tableName = $tableName;
    }

    public function getAll() {
        $query = $this->dbConnection->prepare("SELECT * FROM $this->tableName");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getById($id) {
        if (is_numeric($id) && $id > 0) {
            $query = $this->dbConnection->prepare("SELECT * FROM $this->tableName WHERE id = :id");
            $query->execute([':id' => $id]);
            return $query->fetch(\PDO::FETCH_OBJ);
        }
        return false;
    }

}
