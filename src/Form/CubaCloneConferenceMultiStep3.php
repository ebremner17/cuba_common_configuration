<?php

/**
 * @file
 * Contains \Drupal\cuba_common_configuration\Form\CubaCloneConferenceMultiStep3.
 */

namespace Drupal\cuba_common_configuration\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class CubaCloneConferenceMultiStep3 extends CubaCloneConferenceMultiStepBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'cuba_clone_conference_multistep_3';
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

    // Step through each of the conference sections and get the values.
    foreach ($this->store->get('conference_sections_to_clone') as $cstbc) {
      if ($cstbc !== 0) {
        $sections[] = $cstbc;
      }
    }

    // The conference sections to be clone list.
    $form['conference_sections_to_clone_list'] = [
      '#type' => 'markup',
      '#markup' => $this->getNodeItemList($sections),
    ];

    // Get the conference name terms.
    $cuba_terms = $this->getConferenceNames();

    // Set the choose from option.
    $options[] = '-- To conference --';

    // Step through each of the terms and get set the options.
    foreach ($cuba_terms as $cuba_term) {
      if ($this->store->get('from_conference') !== $cuba_term['tid']) {
        $options[$cuba_term['tid']] = $cuba_term['name'];
      }
    }

    // The from conference select element.
    $form['to_conference'] = [
      '#type' => 'select',
      '#title' => $this->t('Clone to conference name'),
      '#description' => $this->t('Choose the conference to clone to'),
      '#options' => $options,
      '#default_value' => $this->store->get('to_conference') ?: '',
      '#required' => TRUE,
    ];

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('cuba_common_configuration.cuba_clone_conference_2'),
    );

    // Set the value of the submit button.
    $form['actions']['submit']['#value'] = $this->t('Next');

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