<?php

/**
 * @file
 * Contains \Drupal\cuba_common_configuration\Form\CubaCloneConferenceMultiStep4.
 */

namespace Drupal\cuba_common_configuration\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class CubaCloneConferenceMultiStep4 extends CubaCloneConferenceMultiStepBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'cuba_clone_conference_multistep_4';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    // A message to ensure that what the user wants to clone.
    $form['clone_message'] = [
      '#type' => 'markup',
      '#markup' => '<h2>Confirm that you wish to clone the following.</h2>  <h4>Be sure that this is what you want to do as it can cause errors and content that you are not intending.</h4>',
      '#prefix' => '<div class="cuba-confirm-message">',
      '#suffix' => '</div>',
    ];

    // Step through each of the conference sections and get the values.
    foreach ($this->store->get('conference_sections_to_clone') as $cstbc) {
      if ($cstbc !== 0) {
        $sections[] = $cstbc;
      }
    }

    $html = '<div class="cuba-from-clone">Cloning from <strong>' . $this->getConferenceName($this->store->get('from_conference')) . '</strong></div>';
    $html .= '<div class="cuba-clone-list">' . $this->getNodeItemList($sections) . '</div>';
    $html .= '<div class="cuba-to-clone">Cloning to <strong>' . $this->getConferenceName($this->store->get('to_conference')) . '</strong></div>';

    // Display the conference name that we are cloning from.
    $form['clone_info_message'] = [
      '#type' => 'markup',
      '#markup' => $html,
      '#prefix' => '<div class="cuba-clone-info">',
      '#suffix' => '</div>',
    ];

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('cuba_common_configuration.cuba_clone_conference_3'),
    );

    // Set the value of the submit button.
    $form['actions']['submit']['#value'] = $this->t('Confirm');

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