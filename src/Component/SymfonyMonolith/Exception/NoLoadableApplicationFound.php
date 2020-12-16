<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Exception;

use LogicException;

final class NoLoadableApplicationFound extends LogicException implements Throwable
{
    public static function create(): self
    {
        return new self('Could not find an application to load.');
    }
}
