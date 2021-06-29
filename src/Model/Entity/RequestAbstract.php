<?php

namespace Blexr\Model\Entity;

abstract class RequestAbstract {

    const STATUS_PENDING = 'PENDING';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_APPROUVED = 'APPROUVED';

    public static function isStatusValid($status) {
        return in_array($status, [Request::STATUS_APPROUVED, Request::STATUS_PENDING, Request::STATUS_REJECTED]);
    }

}
