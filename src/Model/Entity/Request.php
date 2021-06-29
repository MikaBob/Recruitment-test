<?php

namespace Blexr\Model\Entity;

use Blexr\Model\Entity\RequestAbstract;

class Request extends RequestAbstract implements \JsonSerializable {

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var \Datetime
     */
    private $startDate;

    /**
     * @var \Datetime|null
     */
    private $endDate;

    /**
     * @var string
     */
    private $status;

    function getId(): int {
        return $this->id;
    }

    function getUserId(): int {
        return $this->userId;
    }

    function getStartDate(): \Datetime {
        return $this->startDate;
    }

    function getEndDate(): ?\Datetime {
        return $this->endDate;
    }

    function getStatus(): STRING {
        return $this->status;
    }

    function setId(int $id): void {
        $this->id = $id;
    }

    function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    function setStartDate(\Datetime $startDate): void {
        $this->startDate = $startDate;
    }

    function setEndDate(?\Datetime $endDate): void {
        $this->endDate = $endDate;
    }

    function setStatus(string $status): void {
        if (self::isStatusValid($status)) {
            $this->status = $status;
        } else {
            throw new Exception('Can not set ' . $status . ' as statuts.');
        }
    }

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate(),
            'status' => $this->getStatus()
        ];
    }

}
