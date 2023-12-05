<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\Session\Handler;
use OnixSystemsPHP\HyperfCore\Constants\Time;

return [
    'handler' => Handler\RedisHandler::class,
    'options' => [
        'connection' => 'default',
        'gc_maxlifetime' => Time::WEEK,
        'session_name' => 'SID',
        'domain' => null,
        'cookie_lifetime' => Time::WEEK,
    ],
];
