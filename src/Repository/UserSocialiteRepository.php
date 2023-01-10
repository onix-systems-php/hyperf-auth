<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Repository;

use Hyperf\Utils\Str;
use OnixSystemsPHP\HyperfAuth\Model\UserSocialite;
use OnixSystemsPHP\HyperfCore\Model\AbstractModel;
use OnixSystemsPHP\HyperfCore\Model\Builder;
use OnixSystemsPHP\HyperfCore\Repository\AbstractRepository;

/**
 * @method UserSocialite create(array $data)
 * @method UserSocialite update(UserSocialite $model, array $data)
 * @method UserSocialite save(UserSocialite $model)
 * @method bool delete(UserSocialite $model)
 * @method UserSocialite associate(UserSocialite $model, string $relation, AbstractModel $related)
 * @method Builder|UserSocialiteRepository finder(string $type, ...$parameters)
 * @method null|UserSocialite fetchOne(bool $lock, bool $force)
 */
class UserSocialiteRepository extends AbstractRepository
{
    protected string $modelClass = UserSocialite::class;

    public function getByEmail(string $email, bool $lock = false, bool $force = false): ?UserSocialite
    {
        return $this->finder('email', $email)->fetchOne($lock, $force);
    }

    public function getByProviderData(
        string $provider,
        string $providerId,
        bool $lock = false,
        bool $force = false
    ): ?UserSocialite {
        return $this->finder('providerName', $provider)->finder('providerId', $providerId)->fetchOne($lock, $force);
    }

    public function getByUserProvider(
        string $providerName,
        int $userId,
        bool $lock = false,
        bool $force = false
    ): ?UserSocialite {
        return $this->finder('providerName', $providerName)->finder('userId', $userId)->fetchOne($lock, $force);
    }

    public function scopeId(Builder $query, int $id): void
    {
        $query->where('id', '=', $id);
    }

    public function scopeEmail(Builder $query, string $email): void
    {
        $query->whereRaw('LOWER(email) = ? ', Str::lower($email));
    }

    public function scopeProviderId(Builder $query, string $providerId): void
    {
        $query->where('provider_id', '=', $providerId);
    }

    public function scopeProviderName(Builder $query, string $providerName): void
    {
        $query->where('provider_name', '=', $providerName);
    }

    public function scopeUserId(Builder $query, int $userId): void
    {
        $query->where('user_id', '=', $userId);
    }
}
