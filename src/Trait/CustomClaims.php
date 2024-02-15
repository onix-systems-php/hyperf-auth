<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Trait;

trait CustomClaims
{
    /**
     * Custom claims.
     */
    protected array $customClaims = [];

    /**
     * Get the custom claims.
     */
    public function getCustomClaims(): array
    {
        return $this->customClaims;
    }

    /**
     * Get the custom claim by name.
     *
     * @return array
     */
    public function getCustomClaim(string $name): mixed
    {
        return $this->getCustomClaims()[$name] ?? null;
    }

    /**
     * Set the custom claims.
     *
     * @return $this
     */
    public function customClaims(array $customClaims): static
    {
        $this->customClaims = $customClaims;

        return $this;
    }

    /**
     * Alias to set the custom claims.
     *
     * @return $this
     */
    public function claims(array $customClaims): static
    {
        return $this->customClaims($customClaims);
    }
}
