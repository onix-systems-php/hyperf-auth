<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace OnixSystemsPHP\HyperfAuth\Test\Mocks;

use Hyperf\Contract\ContainerInterface;

class TestContainer implements ContainerInterface
{
    private array $container = [];

    public function set(string $name, $entry): void
    {
        $this->container[$name] = $entry;
    }

    public function unbind(string $name): void
    {
        // DO NOTHING
    }

    public function get(string $id)
    {
        return $this->container[$id] ?? null;
    }

    public function define(string $name, $definition): void
    {
        // DO NOTHING
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->container);
    }

    public function make(string $name, array $parameters = [])
    {
        return $this->get($name);
    }
}
