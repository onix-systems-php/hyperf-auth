<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Controller;

use Carbon\Carbon;
use Hyperf\Redis\Redis;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\SocketIOServer\Socket;
use Hyperf\SocketIOServer\SocketIOConfig;
use Hyperf\WebSocketServer\Sender;
use OnixSystemsPHP\HyperfAuth\AuthManager;
use OnixSystemsPHP\HyperfAuth\Constants\WSAuth;
use OnixSystemsPHP\HyperfAuth\SessionManager;

class WebSocketController extends BaseNamespace
{
    public function __construct(
        Sender $sender,
        SidProviderInterface $sidProvider,
        protected AuthManager $authManager,
        protected SessionManager $sessionManager,
        protected Redis $redis,
        ?SocketIOConfig $config = null,
    ) {
        parent::__construct($sender, $sidProvider, $config);
    }

    #[Event('connect')]
    public function connect(Socket $socket, $data)
    {
        // do nothing
    }

    #[Event('authenticate')]
    public function authenticate(Socket $socket, $data)
    {
        $user = $this->authManager->tokenGuard()->fromAccessToken($data['access_token']);

        if (empty($user)) {
            $socket->emit('authentication', null);
            return;
        }

        $sessionId = $this->sessionManager->getSession()->getId();

        $this->redis->hSet(WSAuth::USER_SOCKET, (string) $user->getId(), $socket->getSid());
        $this->redis->hSet(WSAuth::SOCKET_SESSION, $socket->getSid(), $sessionId);
        $this->redis->hSet(WSAuth::CONNECT_TIMESTAMP, (string) $user->getId(), (string) Carbon::now()->timestamp);
        $socket->emit('authentication', $user->getId());
    }

    #[Event('disconnect')]
    public function disconnect(Socket $socket)
    {
        $user = $this->sessionManager->user();

        $this->redis->hDel(WSAuth::SOCKET_SESSION, $socket->getSid());
        if (! empty($user)) {
            $socket->leaveAll();

            $this->redis->hDel(WSAuth::USER_SOCKET, (string) $user->getId());
            $this->redis->hSet(WSAuth::CONNECT_TIMESTAMP, (string) $user->getId(), (string) Carbon::now()->timestamp);
        }
    }
}
