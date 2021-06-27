<?php

namespace Blexr\Model;

abstract class AbstractDAO {

    protected $dbConnection;
    protected $tableName;

    public function __construct() {
        $this->dbConnection = new \PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }

    public function getAll() {
        $query = $this->dbConnection->prepare("SELECT * FROM $this->tableName");
        $query->execute();
        return $query->fetchAll();
    }

}
