<?php

namespace Blexr\Model\Entity;

abstract class UserAbstract {

    const DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE = 'MOL';
    const DYNAMIC_FIELD_EMAIL_ACCESS_GRANTED = 'EAG';
    const DYNAMIC_FIELD_GIT_REPOSITORY_GRANTED = 'GRG';
    const DYNAMIC_FIELD_JIRA_ACCESS_GRANTED = 'JAG';

    public function __construct() {
        $this->setDynamicFieldMicrosoftOffice(false);
        $this->setDynamicFieldEmailAccess(false);
        $this->setDynamicFieldGitRepository(false);
        $this->setDynamicFieldJira(false);
    }

    public function getDynamicFieldMicrosoftOffice() {
        $dynamicFields = $this->getDynamicFields() ?: [];
        return isset($dynamicFields[self::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE]) ? $dynamicFields[self::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE] : null;
    }

    public function setDynamicFieldMicrosoftOffice(bool $isGranted) {
        $dynamicFields = $this->getDynamicFields() ?: [];
        $dynamicFields[self::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE] = $isGranted;
        $this->setDynamicFields($dynamicFields);
    }

    public function getDynamicFieldEmailAccess() {
        $dynamicFields = $this->getDynamicFields() ?: [];
        return isset($dynamicFields[self::DYNAMIC_FIELD_EMAIL_ACCESS_GRANTED]) ? $dynamicFields[self::DYNAMIC_FIELD_EMAIL_ACCESS_GRANTED] : null;
    }

    public function setDynamicFieldEmailAccess(bool $isGranted) {
        $dynamicFields = $this->getDynamicFields() ?: [];
        $dynamicFields[self::DYNAMIC_FIELD_EMAIL_ACCESS_GRANTED] = $isGranted;
        $this->setDynamicFields($dynamicFields);
    }

    public function getDynamicFieldGitRepository() {
        $dynamicFields = $this->getDynamicFields() ?: [];
        return isset($dynamicFields[self::DYNAMIC_FIELD_GIT_REPOSITORY_GRANTED]) ? $dynamicFields[self::DYNAMIC_FIELD_GIT_REPOSITORY_GRANTED] : null;
    }

    public function setDynamicFieldGitRepository(bool $isGranted) {
        $dynamicFields = $this->getDynamicFields() ?: [];
        $dynamicFields[self::DYNAMIC_FIELD_GIT_REPOSITORY_GRANTED] = $isGranted;
        $this->setDynamicFields($dynamicFields);
    }

    public function getDynamicFieldJira() {
        $dynamicFields = $this->getDynamicFields() ?: [];
        return isset($dynamicFields[self::DYNAMIC_FIELD_JIRA_ACCESS_GRANTED]) ? $dynamicFields[self::DYNAMIC_FIELD_JIRA_ACCESS_GRANTED] : null;
    }

    public function setDynamicFieldJira(bool $isGranted) {
        $dynamicFields = $this->getDynamicFields() ?: [];
        $dynamicFields[self::DYNAMIC_FIELD_JIRA_ACCESS_GRANTED] = $isGranted;
        $this->setDynamicFields($dynamicFields);
    }

}
