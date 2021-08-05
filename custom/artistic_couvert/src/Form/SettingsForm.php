<?php

namespace Drupal\artistic_couvert\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure artistic-couvert settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'artistic_couvert_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['artistic_couvert.settings'];
  }

  /**
   * {@inheritdoc}
   */



  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['status_couvert'] = [
      '#type' => 'checkbox',
      '#description' => t('Selecione se está ocorrendo ou não um couvert artistico no momento'),
      '#title' => $this->t('Habilitado'),
      '#default_value' => $this->config('artistic_couvert.settings')->get('status_couvert'),

    ];
 
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('artistic_couvert.settings')
      ->set('status_couvert', $form_state->getValue('status_couvert'))
      ->save();
    parent::submitForm($form, $form_state);
  }
    // O field é salvo na tabela drupal9/config/name como artistic_couvert.settings como Boolean

}

