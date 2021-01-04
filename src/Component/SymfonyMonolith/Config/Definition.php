<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Definition implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('symfony_monolith');

        /** @var ArrayNodeDefinition $root */
        $root = $builder->getRootNode();
        $children = $root->children();

        $application = $children->arrayNode('applications')->requiresAtLeastOneElement()->arrayPrototype()->children();
        $application->scalarNode('name')->isRequired();

        $kernel = $application->arrayNode('kernel')->children();
        $kernel->scalarNode('class')->isRequired();
        $kernel->arrayNode('arguments')->scalarPrototype();

        return $builder;
    }
}
