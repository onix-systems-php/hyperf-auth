<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth;

use Hyperf\Session\Session as SessionBase;

class Session extends SessionBase
{
    public function __construct($name, \SessionHandlerInterface $handler, $id = null)
    {
        parent::__construct($name, $handler, $id);
    }

    public function generateSessionId(): string
    {
        return $this->getPrefix() . parent::generateSessionId();
    }

    public function isValidId(string $id): bool
    {
        return str_starts_with($id, $this->getPrefix())
            && (strlen($id) - strlen($this->getPrefix()) === 40);
    }

    private function getPrefix(): string
    {
        return 'sid:';
    }
}
