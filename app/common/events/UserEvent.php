<?php


namespace app\common\events;


use app\common\model\SystemUser;

class UserEvent extends BaseEvent
{

    /** @var SystemUser */
    public $user;

    public function __construct(SystemUser $user)
    {
        $this->user = $user;
    }

}