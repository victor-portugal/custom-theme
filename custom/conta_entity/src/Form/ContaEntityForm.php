<?php

namespace Drupal\conta_entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the conta entity edit forms.
 */
class ContaEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New conta %label has been created.', $message_arguments));
      $this->logger('conta_entity')->notice('Created new conta %label', $logger_arguments);

      $mesa = $form_state->getValue('field_conta_mesa_relacionada');
      $nid = NULL;
      $nid = $mesa[0]['target_id'];
      $database = \Drupal::database();

      $database->query("UPDATE pedido_field_data pedido SET pedido.status_pedido = 'pago' where nome_mesa =" . $nid);
    }
    else {
      $this->messenger()->addStatus($this->t('The conta %label has been updated.', $message_arguments));
      $this->logger('conta_entity')->notice('Updated new conta %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.conta_entity.canonical', ['conta_entity' => $entity->id()]);
  }

}
