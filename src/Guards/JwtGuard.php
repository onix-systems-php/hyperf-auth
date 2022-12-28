<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Guards;

use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfAuth\SessionManager;
use Psr\Http\Message\ServerRequestInterface;
use Qbhy\HyperfAuth\Authenticatable as BaseAuthenticatable;
use Qbhy\HyperfAuth\Guard\AbstractAuthGuard;
use Qbhy\HyperfAuth\UserProvider;
use Qbhy\SimpleJwt\Exceptions\InvalidTokenException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;
use Qbhy\SimpleJwt\JWTManager;

class JwtGuard extends AbstractAuthGuard
{
    protected JWTManager $jwtManager;

    protected SessionManager $sessionManager;

    protected RequestInterface $request;

    protected ConfigInterface $appConfig;

    protected string $headerName = 'Authorization';

    public function __construct(
        array $config,
        string $name,
        UserProvider $userProvider,
        RequestInterface $request,
        SessionManager $sessionManager,
        ConfigInterface $appConfig,
    ) {
        parent::__construct($config, $name, $userProvider);
        $this->headerName = $config['header_name'] ?? 'Authorization';
        $this->jwtManager = new JWTManager($config);
        $this->request = $request;
        $this->sessionManager = $sessionManager;
        $this->appConfig = $appConfig;
    }

    public function id(): ?int
    {
        return $this->user()?->getId();
    }

    public function login(BaseAuthenticatable $user): AuthTokensDTO
    {
        $this->sessionManager->getSession()->invalidate();
        $session = $this->getSession();
        $refreshToken = $this->generateHashString();
        $accessToken = $this->getJwtManager()
            ->make([
                'sub' => $user->getId(),
                'iss' => $this->appConfig->get('domain_api'),
                'session_token' => $session->getId(),
            ])
            ->token();

        $session->set('refresh_token', $refreshToken);
        $session->set('user', $user);

        return AuthTokensDTO::make([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    public function user(): Authenticatable|null
    {
        try {
            $accessToken = $this->getAccessToken();
            $user = null;
            if (! empty($accessToken)) {
                $jwtPayload = $this->getJwtManager()->parse($accessToken)->getPayload();
                $session = $this->getSession($jwtPayload['session_token']);
                if ($session->has('user') && ! empty($session->get('user'))) {
                    $user = $session->get('user');
                } elseif (! empty($jwtPayload['sub'])) {
                    /** @var Authenticatable $user */
                    $user = $this->userProvider->retrieveByCredentials($jwtPayload['sub']);
                    $session->set('user', $user);
                }
            }
            return $user;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function check(): bool
    {
        return $this->user() instanceof Authenticatable;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function refresh(): AuthTokensDTO
    {
        $accessToken = $this->getAccessToken();
        $refreshToken = $this->getRefreshToken();
        if (empty($accessToken) || empty($refreshToken)) {
            throw new InvalidTokenException(__('exceptions.auth.invalid_token'));
        }
        $payload = $this->getPayload($accessToken);
        $session = $this->getSession($payload['session_token']);
        if (empty($session) || $session->get('refresh_token') !== $refreshToken) {
            throw new TokenExpiredException(__('exceptions.auth.session_expired'));
        }
        $session->set('refresh_token', $this->generateHashString());
        $session->set('user', $this->userProvider->retrieveByCredentials($payload['sub']));

        try {
            $jwt = $this->getJwtManager()->parse($accessToken);
        } catch (TokenExpiredException $exception) {
            $jwt = $exception->getJwt();
        }

        $this->getJwtManager()->addBlacklist($jwt);

        $session->save();

        return AuthTokensDTO::make([
            'access_token' => $this->getJwtManager()->refresh($jwt)->token(),
            'refresh_token' => $session->get('refresh_token'),
        ]);
    }

    public function logout(): bool
    {
        $accessToken = $this->getAccessToken();
        if (! empty($accessToken)) {
            $this->getJwtManager()->addBlacklist(
                $this->getJwtManager()->parse($accessToken)
            );
            return true;
        }
        return false;
    }

    public function getAccessToken(): ?string
    {
        $result = null;
        if (Context::get(ServerRequestInterface::class)) {
            $header = $this->request->header($this->headerName, '');
            if (Str::startsWith($header, 'Bearer ')) {
                $result = Str::substr($header, 7);
            } elseif ($this->request->has('access_token')) {
                $result = $this->request->input('access_token');
            }
        }
        return $result;
    }

    public function getRefreshToken(): ?string
    {
        if (
            Context::get(ServerRequestInterface::class)
            && $this->request->has('refresh_token')
        ) {
            return $this->request->input('refresh_token');
        }
        return null;
    }

    private function getSession(?string $sessionToken = null): SessionInterface
    {
        if (! $this->sessionManager->isSessionStarted()) {
            return $this->sessionManager->startFromId($sessionToken);
        }
        $session = $this->sessionManager->getSession();
        if (empty($sessionToken) || $session->getId() === $sessionToken) {
            return $session;
        }
        $session->invalidate();
        return $this->sessionManager->startFromId($sessionToken);
    }

    private function getPayload(?string $jwtToken = null): ?array
    {
        return ! empty($jwtToken)
            ? $this->getJwtManager()->justParse($jwtToken)->getPayload()
            : null;
    }

    private function getJwtManager(): JWTManager
    {
        return $this->jwtManager;
    }

    private function generateHashString(int $length = 30): string
    {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}
