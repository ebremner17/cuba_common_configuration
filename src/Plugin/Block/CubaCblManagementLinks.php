<?php

namespace Drupal\cuba_common_configuration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CubaCblManagementLinks block.
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

    $user = \Drupal::currentUser();

    // Check if user can administer main menu items.
    if ($user->hasPermission('administer main menu items')) {

      // Create link to main menu.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/structure/menu/manage/main')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Manage main menu',
      ];
    }

    // Check if user can administer conference menu.
    if ($user->hasPermission('administer cuba-menu-conferences menu items')) {

      // Create link to conference menu.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/structure/menu/manage/cuba-menu-conferences')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Manage conference menu',
      ];
    }

    // Check if user can create and edit conference name terms.
    if ($user->hasPermission('create terms in cuba_voc_conference_name') || $user->hasPermission('edit terms in cuba_voc_conference_name')) {

      // Create link to conference taxonomy terms.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/structure/taxonomy/manage/cuba_voc_conference_name/overview')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Manage conference names',
      ];
    }

    // Check if user can create and edit conference name terms.
    if ($user->hasPermission('administer users')) {

      // Create link to conference taxonomy terms.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/people')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Manage users',
      ];
    }

    // Set build array.
    $build = [
      '#theme' => 'cuba_management_links',
      '#links' => $links,
    ];

    return $build;
  }
}
