<?php

namespace Blexr\Model\Entity;

class User {

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \Datetime
     */
    private $creationDate;

    /**
     * @var \Datetime|null
     */
    private $lastLogin;

    /**
     * @var string|null
     */
    private $dynamicFields;

    function getFirstName(): ?string {
        return $this->firstName;
    }

    function getLastName(): ?string {
        return $this->lastName;
    }

    function getEmail(): string {
        return $this->email;
    }

    function getPassword(): string {
        return $this->password;
    }

    function getCreationDate(): \Datetime {
        return $this->creationDate;
    }

    function getLastLogin(): ?\Datetime {
        return $this->lastLogin;
    }

    function getDynamicFields(): ?string {
        return $this->dynamicFields;
    }

    function setFirstName(?string $firstName): void {
        $this->firstName = $firstName;
    }

    function setLastName(?string $lastName): void {
        $this->lastName = $lastName;
    }

    function setEmail(string $email): void {
        $this->email = $email;
    }

    function setPassword(string $password): void {
        $this->password = $password;
    }

    function setCreationDate(\Datetime $creationDate): void {
        $this->creationDate = $creationDate;
    }

    function setLastLogin(?\Datetime $lastLogin): void {
        $this->lastLogin = $lastLogin;
    }

    function setDynamicFields(?string $dynamicFields): void {
        $this->dynamicFields = $dynamicFields;
    }

}
