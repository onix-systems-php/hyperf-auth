<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\SocketIOServer\Collector\SocketIORouter;
use OnixSystemsPHP\HyperfAuth\Controller\WebSocketController;

SocketIORouter::addNamespace('/', WebSocketController::class);
