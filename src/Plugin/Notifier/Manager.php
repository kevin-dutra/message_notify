<?php

namespace Drupal\message_notify\Plugin\Notifier;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\message\MessageInterface;

/**
 * Notifier plugin manager.
 */
class Manager extends DefaultPluginManager {

  /**
   * Constructs a new class instance.
   *
   * @param string|bool $subdir
   *   The plugin's subdirectory, for example Plugin/views/filter.
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param string|null $plugin_interface
   *   The interface each plugin should implement.
   * @param string $plugin_definition_annotation_name
   *   The name of the annotation that contains the plugin definition.
   */
  public function __construct($subdir, \Traversable $namespaces, ModuleHandlerInterface $module_handler, $plugin_interface, $plugin_definition_annotation_name) {
    parent::__construct($subdir, $namespaces, $module_handler, $plugin_interface, $plugin_definition_annotation_name);
    $this->alterInfo('message_notifiers');
  }

  /**
   * {@inheritdoc}
   *
   * Allow the message entity to be passed to the constructor.
   */
  public function createInstance($plugin_id, array $configuration = [], MessageInterface $message = NULL) {
    $plugin_definition = $this->getDefinition($plugin_id);
    $plugin_class = DefaultFactory::getPluginClass($plugin_id, $plugin_definition);
    // If the plugin provides a factory method, pass the container to it.
    if (is_subclass_of($plugin_class, ContainerFactoryPluginInterface::class)) {
      $plugin = $plugin_class::create(\Drupal::getContainer(), $configuration, $plugin_id, $plugin_definition, $message);
    }
    else {
      $plugin = new $plugin_class($configuration, $plugin_id, $plugin_definition, $message);
    }
    return $plugin;
  }

}
