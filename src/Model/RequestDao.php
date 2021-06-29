<?php

namespace Blexr\Model;

use Blexr\Model\Entity\Request;

class RequestDAO extends AbstractDAO {

    protected $tableName = 'request';

    public function __construct() {
        parent::__construct($this->tableName);
    }

    public function insert(Request $request): \PDOStatement {
        $query = $this->dbConnection->prepare(''
                . 'INSERT INTO `' . $this->tableName . '` (userId, startDate, endDate, status) '
                . 'VALUES (:userId, :startDate, :endDate, :status)');

        $query->execute([
            ':userId' => $request->getUserId(),
            ':startDate' => $request->getStartDate()->format('c'),
            ':endDate' => $request->getEndDate()->format('c'),
            ':status' => $request->getStatus()
        ]);

        return $query;
    }

    public function update(Request $request): \PDOStatement {
        $query = $this->dbConnection->prepare(''
                . 'UPDATE `' . $this->tableName . '` SET userId = :userId, startDate = :startDate, endDate = :endDate, status = :status '
                . 'WHERE id = :id');

        $query->execute([
            ':userId' => $request->getUserId(),
            ':startDate' => $request->getStartDate()->format('c'),
            ':endDate' => $request->getEndDate()->format('c'),
            ':status' => $request->getStatus(),
            ':id' => $request->getId()
        ]);

        return $query;
    }

    public function delete(Request $request) {
        $query = $this->dbConnection->prepare('DELETE FROM`' . $this->tableName . '` WHERE id = :id');

        $query->execute([
            ':id' => $request->getId()
        ]);

        return $query;
    }

    public function getById($id): ?Request {
        $obj = parent::getById($id);

        if ($obj !== false) {
            return $this->fromObjToRequest($obj);
        }
        return null;
    }

    private function fromObjToRequest($obj): Request {
        $request = new Request();
        $request->setId($obj->id);
        $request->setUserId($obj->userId);
        $request->setStartDate($obj->startDate);
        $request->setEndDate($obj->endDate);
        $request->setStatus($obj->status);

        return $request;
    }

}
