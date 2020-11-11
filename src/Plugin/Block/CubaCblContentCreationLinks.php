<?php

namespace Drupal\cuba_common_configuration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CubaCblContentCreationLinks block.
 *
 * @Block(
 *   id = "cuba_cbl_content_creation_links",
 *   admin_label = @Translation("Content links"),
 * )
 */
class CubaCblContentCreationLinks extends BlockBase implements ContainerFactoryPluginInterface {

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

    // Check if user can create web pages.
    if ($user->hasPermission('create cuba_ct_web_page content')) {

      // Create link to main menu.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/node/add/cuba_ct_web_page')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Add new web page',
      ];
    }

    // Check if user can create conference sections.
    if ($user->hasPermission('create cuba_ct_conference_section content')) {

      // Create link to conference menu.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/node/add/cuba_ct_conference_section')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Add new conference section',
      ];
    }

    // Check if user can create web pages.
    if ($user->hasPermission('create cuba_ct_board_of_directors content')) {

      // Create link to main menu.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/node/add/cuba_ct_board_of_directors')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Add new board member',
      ];
    }

    // Check if user can create clone conferences.
    if ($user->hasPermission('clone cuba conferences')) {

      // Create link to conference menu.
      $link = 'https://' . \Drupal::request()->getHost() . Url::fromUri('internal:/admin/clone_conference/1')->toString();

      $links[] = [
        'link' => $link,
        'title' => 'Clone a conference',
      ];
    }

    // Set build array.
    $build = [
      '#theme' => 'cuba_content_creation_links',
      '#links' => $links,
    ];

    return $build;
  }
}
