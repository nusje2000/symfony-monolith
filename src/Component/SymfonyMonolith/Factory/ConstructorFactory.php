<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Factory;

use Acme\Component\SymfonyMonolith\Exception\InvalidKernelFactory;
use Symfony\Component\HttpKernel\KernelInterface;

final class ConstructorFactory implements KernelFactory
{
    /**
     * @var string
     */
    private $kernelClass;

    /**
     * @var array<mixed>
     */
    private $arguments;

    /**
     * @param class-string $kernelClass
     */
    public function __construct(string $kernelClass, ...$arguments)
    {
        $this->kernelClass = $kernelClass;
        $this->arguments = $arguments;
    }

    public function create(): KernelInterface
    {
        $kernel = new $this->kernelClass(...$this->arguments);

        if (!$kernel instanceof KernelInterface) {
            throw InvalidKernelFactory::createdInvalidKernel($kernel);
        }

        return $kernel;
    }
}
