<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Application extends SymfonyConsoleApplication
{
    /**
     * @return InputDefinition
     */
    public function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption('--application', '-a', InputOption::VALUE_REQUIRED, 'Defines the application that the command should be executed on.')
        );

        return $definition;
    }
}
