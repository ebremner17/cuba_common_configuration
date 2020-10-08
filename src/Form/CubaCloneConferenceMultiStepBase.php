<?php

/**
 * @file
 * Contains \Drupal\cuba_common_configuration\Form\CubaCloneConferenceMultiStepBase.
 */

namespace Drupal\cuba_common_configuration\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class CubaCloneConferenceMultiStepBase extends FormBase {

  /**
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $store;

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface */
  protected $entityTypeManager;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a \Drupal\demo\Form\Multistep\MultistepFormBase.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user, MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;
    $this->messenger = $messenger;
    $this->entityTypeManager = $entity_type_manager;

    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private'),
      $container->get('session_manager'),
      $container->get('current_user'),
      $container->get('messenger'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = array();
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    return $form;
  }

  /**
   * Saves the data from the multistep form.
   */
  protected function saveData() {

    // Logic for saving data goes here...
    //$this->deleteStore();
    $this->messenger->addMessage('The form has been saved.');
  }

  /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deleteStore() {

    // Keys to be deleted.
    $keys = ['from_conference', 'conference_sections_to_clone', 'to_conference'];

    // Step through each key and delete them.
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }

  /**
   * Function to get the terms for the Cuba Conference names.
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getConferenceNames() {

    // Load in all the terms (conference names).
    $terms = $this->entityTypeManager->getStorage("taxonomy_term")->loadTree('cuba_voc_conference_name', $parent = 0, $max_depth = NULL, $load_entities = FALSE);

    // Step through each of the terms and setup array with tid and name.
    foreach ($terms as $term) {
      $cuba_terms[] = [
        'tid' => $term->tid,
        'name' => $term->name,
      ];
    }

    // Return the conference names.
    return $cuba_terms;
  }

  /**
   * Get the conference name from the tid.
   *
   * @param int $tid
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getConferenceName(int $tid) {

    // Get the term.
    $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);

    // Return the term name.
    return $term->name->value;
  }

  /**
   * A function to get all the nodes with a specific tid.
   *
   * @param int $tid
   * @return array
   */
  public function getNodes(int $tid) {

    // Query to get the nodes.
    $query = \Drupal::entityQuery('node');
    $query->condition('field_cuba_cs_conference_name', $tid);

    // Get the nids from teh query.
    $nids = $query->execute();

    // Step through each node and load it, get the nid and title.
    foreach ($nids as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $nodes[] = [
        'nid' => $nid,
        'title' => $node->getTitle(),
      ];
    }

    return $nodes;
  }

  /**
   * Function to get an item list of the nodes to be cloned.
   *
   * @param array $nids
   * @return string
   */
  public function getNodeItemList(array $nids) {

    // As long as we have nids, get the list.
    if (count($nids) > 0) {
      $html = '<ul>';
      foreach ($nids as $nid) {
        $node = $this->entityTypeManager->getStorage('node')->load($nid);
        $html .= '<li>' . $node->getTitle() . '</li>';
      }
      $html .= '</ul>';

      return $html;
    }

    return '';
  }
}
