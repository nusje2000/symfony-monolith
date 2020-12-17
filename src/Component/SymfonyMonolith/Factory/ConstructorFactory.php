<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Factory;

use Symfony\Component\HttpKernel\KernelInterface;

final class ConstructorFactory implements KernelFactory
{
    /**
     * @var class-string<KernelInterface>
     */
    private $kernelClass;

    /**
     * @var array<mixed>
     */
    private $arguments;

    /**
     * @param class-string<KernelInterface> $kernelClass
     * @param array<mixed>                  $arguments
     */
    public function __construct(string $kernelClass, ...$arguments)
    {
        $this->kernelClass = $kernelClass;
        $this->arguments = $arguments;
    }

    public function create(): KernelInterface
    {
        return new $this->kernelClass(...$this->arguments);
    }
}
