<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Controller;

use Carbon\Carbon;
use Hyperf\Redis\Redis;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\SocketIOServer\Socket;
use Hyperf\SocketIOServer\SocketIOConfig;
use Hyperf\WebSocketServer\Sender;
use OnixSystemsPHP\HyperfAuth\Constants\WSAuth;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;

class WebSocketController extends BaseNamespace
{

    public function __construct(
        Sender $sender,
        SidProviderInterface $sidProvider,
        ?SocketIOConfig $config = null,
        protected CoreAuthenticatableProvider $authenticatableProvider,
        protected Redis $redis,
    ) {
        parent::__construct($sender, $sidProvider, $config);
    }


    #[Event('connect')]
    public function connect(Socket $socket, $data)
    {
        $user = $this->authenticatableProvider->user();

        if (empty($user)) {
            return;
        }

        if (! $this->redis->hExists(WSAuth::UID_SID, (string) $user->getId())) {
            $this->redis->hSet(WSAuth::UID_SID, (string) $user->getId(), $socket->getSid());
        }
        $this->redis->hSet(WSAuth::CONNECT_TIMESTAMP, (string) $user->getId(), (string) Carbon::now()->timestamp);
    }

    #[Event('disconnect')]
    public function disconnect(Socket $socket)
    {
        $user = $this->authenticatableProvider->user();

        if (! empty($user)) {
            $socket->leaveAll();

            $this->redis->hDel(WSAuth::UID_SID, (string) $user->getId());
            $this->redis->hSet(WSAuth::CONNECT_TIMESTAMP, (string) $user->getId(), (string) Carbon::now()->timestamp);
        }
    }
}
