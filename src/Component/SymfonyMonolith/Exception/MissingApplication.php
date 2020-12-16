<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Exception;

use LogicException;

final class MissingApplication extends LogicException implements Throwable
{
    public static function create(string $name): self
    {
        return new self(sprintf('No application exists named "%s".', $name));
    }
}
