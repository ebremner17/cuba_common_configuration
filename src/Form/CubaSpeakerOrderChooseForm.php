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
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CubaSpeakerOrderChooseForm extends FormBase {

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
  public function __construct(MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager) {
    $this->messenger = $messenger;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = array();

    $conferences = $this->getConferenceNames();

    foreach ($conferences as $conference) {
      $options[$conference['tid']] = $conference['name'];
    }

    // The from conference select element.
    $form['conference'] = [
      '#type' => 'select',
      '#title' => $this->t('Conference'),
      '#description' => $this->t('Choose the conference to order the speakers on.'),
      '#options' => $options,
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );

    return $form;
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
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'cuba_choose_speaker_order_form';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $conference_tid = $form_state->getValue('conference');
    $url = Url::fromUri('internal:/admin/speakers/order/' . $conference_tid);
    $form_state->setRedirectUrl($url);
  }
}
