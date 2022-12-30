<?php

declare(strict_types=1);
use Hyperf\SocketIOServer\Collector\SocketIORouter;

SocketIORouter::addNamespace('/', \OnixSystemsPHP\HyperfAuth\Controller\WebSocketController::class);
