<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Factory;

use Symfony\Component\HttpKernel\KernelInterface;

interface KernelFactory
{
    public function create(): KernelInterface;
}
