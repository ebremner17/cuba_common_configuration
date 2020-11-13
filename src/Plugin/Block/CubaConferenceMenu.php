<?php

namespace Drupal\cuba_common_configuration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CubaConferenceMenublock.
 *
 * @Block(
 *   id = "cuba_cbl_conference_menu",
 *   admin_label = @Translation("Conference menu"),
 * )
 */
class CubaConferenceMenu extends BlockBase implements ContainerFactoryPluginInterface {

  /** @var \Drupal\Core\Menu\MenuLinkTree */
  protected $menuLinkTree;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, MenuLinkTreeInterface $menuLinkTree) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->menuLinkTree = $menuLinkTree;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('menu.link_tree')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // The build array.
    $build = [];

    // Load menu tree with children items.
    $menu = $this->menuLinkTree->load('cuba-menu-conferences', new MenuTreeParameters());

    // Run trough transform, to append access and sort.
    $trees = $this->menuLinkTree->transform($menu, [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ]);

    // Get the current path.
    $current_path = \Drupal::service('path.current')->getPath();

    // Explode the path so that we can use it to compare against taxonomy term.
    $current_path_array = explode('/', $current_path);

    // Get the nid from the path, which is the last element of the array.
    $nid = end($current_path_array);

    // Get the path alias.
    $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);

    // Explode the path alias as well to use a comparison.
    $path = explode('/', $result);

    // Step through the menu tree and get out the values we need.
    foreach ($trees as $tree) {

      // If the tree has children, then get out values.
      // If the tree doesnt have children, means there is nothing published for
      // that conference, so we will not add it to the menu yet.
      if ($tree->hasChildren) {

        // Get the title of the first menu link.
        $title = $tree->link->pluginDefinition['title'];

        // Set a link title to compare with the path.
        $link_title = str_replace(' ', '-', $title);
        $link_title = strtolower($link_title);

        // If now the link title is in the path array, we are in the active menu.
        // Set the active to true or false to be used to determine open or closed.
        if (in_array($link_title, $path)) {
          $conf_menus[$title]['active'] = TRUE;
        }
        else {
          $conf_menus[$title]['active'] = FALSE;
        }

        // Step through each of the subtrees (children) and get values.
        foreach ($tree->subtree as $subtree) {

          // Setup the variables for the menu link.
          $conf_menus[$title]['subtree'][] = [
            'active' => $nid == $subtree->link->pluginDefinition['route_parameters']['node'] ? TRUE : FALSE,
            'title' => $subtree->link->getTitle(),
            'url' => $subtree->link->getUrlObject(),
          ];
        }
      }
    }

    \Drupal::service('page_cache_kill_switch')->trigger();

    // Return the themed array.
    return [
      '#theme' => 'cuba_conference_menu',
      '#conf_menus' => isset($conf_menus) ? $conf_menus: NULL,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
