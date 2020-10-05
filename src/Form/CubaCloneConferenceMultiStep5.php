<?php

/**
 * @file
 * Contains \Drupal\cuba_common_configuration\Form\CubaCloneConferenceMultiStep5.
 */

namespace Drupal\cuba_common_configuration\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class CubaCloneConferenceMultiStep5 extends CubaCloneConferenceMultiStepBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'cuba_clone_conference_multistep_5';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $nids = $this->store->get('conference_sections_to_clone');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('to_conference', $form_state->getValue('to_conference'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('cuba_common_configuration.cuba_clone_conference_4');
  }
}