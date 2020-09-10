<?php

namespace Drupal\cuba_common_configuration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UwCblBlogTeaser block.
 *
 * @Block(
 *   id = "cuba_cbl_management_links",
 *   admin_label = @Translation("Management links"),
 * )
 */
class CubaCblManagementLinks extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a BlockComponentRenderArray object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\uw_cfg_common\Service\UWServiceInterface $uwService
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // The build array.
    $build = [];

    // Create link to main menu.
    $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/structure/menu/manage/main')->toString();

    $links[] = [
      'link' => $link,
      'title' => 'Manage main menu',
    ];

    // Create link to conference menu.
    $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/structure/menu/manage/cuba-menu-conferences')->toString();

    $links[] = [
      'link' => $link,
      'title' => 'Manage conference menu',
    ];

    // Create link to conference taxonomy terms.
    $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/structure/taxonomy/manage/cuba_voc_conference_name/overview')->toString();

    $links[] = [
      'link' => $link,
      'title' => 'Manage conference names',
    ];

    // Set build array.
    $build = [
      '#theme' => 'cuba_management_links',
      '#links' => $links,
    ];

    return $build;
  }
}
