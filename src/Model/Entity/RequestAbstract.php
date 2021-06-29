<?php

namespace Blexr\Model\Entity;

abstract class RequestAbstract {

    const STATUS_PENDING = 'PENDING';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_ACCEPTED = 'ACCEPTED';

    public static function isStatusValid($status) {
        return in_array($status, [Request::STATUS_ACCEPTED, Request::STATUS_PENDING, Request::STATUS_REJECTED]);
    }

}
