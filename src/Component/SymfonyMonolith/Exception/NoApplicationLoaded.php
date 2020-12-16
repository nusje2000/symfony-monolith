<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Exception;

use LogicException;

final class NoApplicationLoaded extends LogicException implements Throwable
{
    public static function create(): self
    {
        return new self('No application loaded.');
    }
}
