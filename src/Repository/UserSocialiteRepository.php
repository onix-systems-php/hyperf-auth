<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Repository;

use Hyperf\Database\Model\Builder;
use Hyperf\Utils\Str;
use OnixSystemsPHP\HyperfAuth\Model\UserSocialite;
use OnixSystemsPHP\HyperfCore\Model\AbstractModel;
use OnixSystemsPHP\HyperfCore\Repository\AbstractRepository;

/**
 * @method UserSocialite create(array $data)
 * @method UserSocialite update(UserSocialite $model, array $data)
 * @method UserSocialite save(UserSocialite $model)
 * @method bool delete(UserSocialite $model)
 * @method UserSocialite associate(UserSocialite $model, string $relation, AbstractModel $related)
 */
class UserSocialiteRepository extends AbstractRepository
{
    protected string $modelClass = UserSocialite::class;

    // -----

    public function getByEmail(string $email, bool $lock = false, bool $force = false): ?UserSocialite
    {
        return $this->fetchOne($this->queryByEmail($email), $lock, $force);
    }

    public function queryByEmail(string $email): Builder
    {
        return $this->query()->whereRaw('LOWER(email) = ? ', Str::lower($email));
    }

    // -----

    public function getById(int $id, bool $lock = false, bool $force = false): ?UserSocialite
    {
        return $this->fetchOne($this->queryById($id), $lock, $force);
    }

    public function queryById(int $id): Builder
    {
        return $this->query()->where('id', $id);
    }

    // -----
    public function isExists(string $providerId, bool $lock = false, bool $force = false): bool
    {
        return $this->fetchOne($this->queryByProviderId($providerId), $lock, $force) !== null;
    }

    public function getByProviderData(
        string $provider,
        string $providerId,
        bool $lock = false,
        bool $force = false
    ): ?UserSocialite {
        return $this->fetchOne($this->queryByProviderData($provider, $providerId), $lock, $force);
    }

    public function getByUserProvider(
        string $providerName,
        int $userId,
        bool $lock = false,
        bool $force = false
    ): ?UserSocialite {
        return $this->fetchOne($this->queryByUserProvider($providerName, $userId), $lock, $force);
    }

    public function getByProviderName(string $providerName, bool $lock = false, bool $force = false): ?UserSocialite
    {
        return $this->fetchOne($this->queryByProviderName($providerName), $lock, $force);
    }

    public function getByUserId(int $userId, bool $lock = false, bool $force = false): ?UserSocialite
    {
        return $this->fetchOne($this->queryByUserId($userId), $lock, $force);
    }

    public function queryByProviderId(string $providerId): ?Builder
    {
        return $this->query()->where('provider_id', $providerId);
    }

    public function queryByProviderName(string $providerName): ?Builder
    {
        return $this->query()->where('provider_name', $providerName);
    }

    public function queryByUserId(int $userId): ?Builder
    {
        return $this->query()->where('user_id', $userId);
    }

    public function queryByProviderData(string $provider, string $providerId): ?Builder
    {
        return $this->query()->where(['provider_id' => $providerId, 'provider_name' => $provider]);
    }

    public function queryByUserProvider(string $providerName, int $userId): ?Builder
    {
        return $this->query()->where(['provider_name' => $providerName, 'user_id' => $userId]);
    }

    protected function fetchOne(Builder $builder, bool $lock, bool $force): ?UserSocialite
    {
        /** @var ?UserSocialite $result */
        $result = parent::fetchOne($builder, $lock, $force);
        return $result;
    }
}
