<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Exception;

use Symfony\Component\HttpKernel\KernelInterface;
use UnexpectedValueException;

final class InvalidKernelFactory extends UnexpectedValueException implements Throwable
{
    /**
     * @param mixed $created
     */
    public static function createdInvalidKernel($created): self
    {
        return new self(sprintf(
            'Expected kernel factory to create an instance of "%s", but "%s" was retuned.',
            KernelInterface::class,
            is_object($created) ? get_class($created) : gettype($created)
        ));
    }
}
