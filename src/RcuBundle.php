<?php

namespace Herytz\RcuBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class RcuBundle extends AbstractBundle
{
  public function configure(DefinitionConfigurator $definition): void
  {
    $definition->rootNode()
      ->children()
      ->scalarNode('upload_status_path')->defaultValue('/uploadStatus')->end()
      ->scalarNode('upload_path')->defaultValue('/upload')->end()
      ->scalarNode('tmp_dir')->defaultValue('tmp')->end()
      ->scalarNode('output_dir')->defaultValue('tmp')->end()
      ->end();
  }

  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');

    $container->parameters()->set('rcu.upload_status_path', $config['upload_status_path']);
    $container->parameters()->set('rcu.upload_path', $config['upload_path']);
    $container->parameters()->set('rcu.tmp_dir', $config['tmp_dir']);
    $container->parameters()->set('rcu.output_dir', $config['output_dir']);
  }
}
