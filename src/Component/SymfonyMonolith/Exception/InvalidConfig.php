<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Exception;

use LogicException;
use Throwable as SplThrowable;

final class InvalidConfig extends LogicException implements Throwable
{
    public static function invalidConfigFile(string $config, string $reason, ?SplThrowable $previous = null): self
    {
        return new self(sprintf('Could not use config "%s" due to "%s".', $config, $reason), 0, $previous);
    }

    public static function invalidString(string $string, string $reason): self
    {
        return new self(sprintf('Could not parse config string "%s" due to "%s".', $string, $reason));
    }
}
