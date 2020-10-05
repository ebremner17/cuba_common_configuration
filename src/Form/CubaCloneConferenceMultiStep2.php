<?php

/**
 * @file
 * Contains \Drupal\cuba_common_configuration\Form\CubaCloneConferenceMultiStep2.
 */

namespace Drupal\cuba_common_configuration\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class CubaCloneConferenceMultiStep2 extends CubaCloneConferenceMultiStepBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'cuba_clone_conference_multistep_2';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    // Display the conference name that we are cloning.
    $form['from_conference_name'] = [
      '#type' => 'markup',
      '#markup' => 'Cloning from ' . $this->getConferenceName($this->store->get('from_conference')),
    ];

    // Get the nodes for the conference to be cloned.
    $nodes = $this->getNodes($this->store->get('from_conference'));

    // Set the options array.
    $options = [];

    // Step through the nodes and setup the options.
    foreach ($nodes as $node) {
      $options[$node['nid']] = $node['title'];
    }

    // The checkbox sections to be cloned element.
    $form['conference_sections_to_clone'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Conference sections to clone'),
      '#description' => $this->t(''),
      '#options' => $options,
      '#default_value' => $this->store->get('conference_sections_to_clone') ?: [],
      '#required' => TRUE,
    ];

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('cuba_common_configuration.cuba_clone_conference_1'),
    );

    // Set the value of the submit button.
    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('conference_sections_to_clone', $form_state->getValue('conference_sections_to_clone'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('cuba_common_configuration.cuba_clone_conference_3');
  }
}