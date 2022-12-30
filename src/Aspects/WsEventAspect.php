<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Aspects;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Redis\Redis;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\Socket;
use OnixSystemsPHP\HyperfAuth\Constants\WSAuth;
use OnixSystemsPHP\HyperfAuth\SessionManager;

#[Aspect]
final class WsEventAspect extends AbstractAspect
{
    public $annotations = [
        Event::class,
    ];

    public function __construct(
        private SessionManager $sessionManager,
        private Redis $redis,
    ) {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        /** @var Socket $socket */
        $socket = $proceedingJoinPoint->getArguments()[0];
        if ($this->redis->hExists(WSAuth::SOCKET_SESSION, $socket->getSid())) {
            $sessionId = $this->redis->hGet(WSAuth::SOCKET_SESSION, $socket->getSid());
            $this->sessionManager->startFromId($sessionId);
        }
        return $proceedingJoinPoint->process();
    }
}
