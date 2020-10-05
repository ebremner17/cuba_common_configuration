<?php

/**
 * @file
 * Contains \Drupal\cuba_common_configuration\Form\CubaCloneConferenceMultiStep1.
 */

namespace Drupal\cuba_common_configuration\Form;

use Drupal\Core\Form\FormStateInterface;

class CubaCloneConferenceMultiStep1 extends CubaCloneConferenceMultiStepBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'cuba_clone_conference_multistep_1';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    // Get the conference name terms.
    $cuba_terms = $this->getConferenceNames();

    // Set the choose from option.
    $options[] = '-- From conference --';

    // Step through each of the terms and get set the options.
    foreach ($cuba_terms as $cuba_term) {
      $options[$cuba_term['tid']] = $cuba_term['name'];
    }

    // The from conference select element.
    $form['from_conference'] = [
      '#type' => 'select',
      '#title' => $this->t('Clone from conference name'),
      '#description' => $this->t('Choose the conference to clone from'),
      '#options' => $options,
      '#default_value' => $this->store->get('from_conference') ?: '',
      '#required' => TRUE,
    ];

    // Set the value of the submit button.
    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('from_conference', $form_state->getValue('from_conference'));
    $form_state->setRedirect('cuba_common_configuration.cuba_clone_conference_2');
  }

}