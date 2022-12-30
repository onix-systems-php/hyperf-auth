<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class WSAuth extends AbstractConstants
{
    public const USER_SOCKET = 'uid-sid';
    public const SOCKET_SESSION = 'sid-session';
    public const CONNECT_TIMESTAMP = 'connect-timestamp';
}
