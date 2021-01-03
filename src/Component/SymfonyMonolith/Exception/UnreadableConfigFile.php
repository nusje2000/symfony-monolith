<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Exception;

use UnexpectedValueException;

final class UnreadableConfigFile extends UnexpectedValueException implements Throwable
{
    public static function create(string $file): self
    {
        return new self(sprintf('Could not read config file "%s".', $file));
    }
}
