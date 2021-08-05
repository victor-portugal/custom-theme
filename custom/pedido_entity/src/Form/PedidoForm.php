<?php

namespace Drupal\pedido_entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Plugin\views\argument_default\CurrentUser;

/**
 * Form controller for the pedido entity edit forms.
 */
class PedidoForm extends ContentEntityForm {

  /**
   * Valida se o status settado é compativel com a role que o implementa e com o status anterior.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $current_role = \Drupal::currentUser()->getRoles();
    $status_setted = array_values($form_state->getValue('status_pedido')[0])[0];
    $current_url = \Drupal::service('path.current')->getPath();
    list($l, $ad, $cnt, $pd, $id, $ed) = explode("/", $current_url);

    // Quando se trata da criação de um pedido novo.
    if ($id == "add") {
      if ($current_role[1] == "garcon") {
        if ($status_setted != "aberto") {
          $form_state->setErrorByName('status_pedido', t('Status do pedido quando criado deve ser aberto!', ['%status_pedido' => "aberto"]));
        }
      }
    }
    // Definições de status que devem ser impedidos sempre (global).
    if ($current_role[1] == "garcon") {
      if ($status_setted == "em_andamento") {
        $form_state->setErrorByName('status_pedido', t('Garçom não pode definir status para  em andamento!', ['%status_pedido' => "em_andamento"]));
      }

      if ($status_setted == "pronto") {
        $form_state->setErrorByName('status_pedido', t('Garçom não pode definir status para pronto!', ['%status_pedido' => "pronto"]));
      }
    }

    if ($current_role[1] == "cozinheiro") {
      if ($status_setted == "aberto") {
        $form_state->setErrorByName('status_pedido', t('Chef não pode definir status para aberto!', ['%status_pedido' => "aberto"]));
      }

      if ($status_setted == "entregue") {
        $form_state->setErrorByName('status_pedido', t('Chef não pode definir status para entregue!', ['%status_pedido' => "entregue"]));
      }

      if ($status_setted == "pago") {
        $form_state->setErrorByName('status_pedido', t('Chef não pode definir status para pago!', ['%status_pedido' => "pronto"]));
      }
    }

    // Quando se trata da edição de um pedido.
    if ($id != "add") {
      $int_id = (int) $id;
      $intval_id = intval($id);
      $status_anterior = \Drupal::entityTypeManager()->getStorage('pedido')->load($int_id)->get('status_pedido')->getValue()[0]['value'];
      if ($current_role[1] == "garcon") {
        if ($status_setted == "aberto" && $status_anterior == "pronto") {
          $form_state->setErrorByName('status_pedido', t('Garçom não pode voltar um pedido pronto para aberto!', ['%status_pedido' => "aberto"]));
        }
        if ($status_setted == "aberto" && $status_anterior == "entregue") {
          $form_state->setErrorByName('status_pedido', t('Garçom não pode voltar um pedido entregue para aberto!', ['%status_pedido' => "aberto"]));
        }
        if ($status_setted == "aberto" && $status_anterior == "pago") {
          $form_state->setErrorByName('status_pedido', t('Garçom não pode voltar um pedido pago para aberto!', ['%status_pedido' => "aberto"]));
        }
        if ($status_setted == "pago" && $status_anterior != "entregue") {
          $form_state->setErrorByName('status_pedido', t('Garçom não pode passar o pedido direto para pago!', ['%status_pedido' => "aberto"]));
        }
        if ($status_setted == "entregue" && $status_anterior == "aberto") {
          $form_state->setErrorByName('status_pedido', t('Garçom não pode passar um pedido aberto para entregue!', ['%status_pedido' => "aberto"]));
        }
      }
      if ($current_role[1] == "cozinheiro") {
        if ($status_setted == "em_andamento" && $status_anterior == "pronto") {
          $form_state->setErrorByName('status_pedido', t('Chef não pode voltar um pedido pronto para em andamento!', ['%status_pedido' => "pronto"]));
        }
        if ($status_setted == "pronto" && $status_anterior == "aberto") {
          $form_state->setErrorByName('status_pedido', t('Chef não pode passar um pedido aberto para pronto!', ['%status_pedido' => "pronto"]));
        }
      }
    }
    parent::validateForm($form, $form_state);
  }

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
      $this->messenger()->addStatus($this->t('New pedido %label has been created.', $message_arguments));
      $this->logger('pedido_entity')->notice('Created new pedido %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The pedido %label has been updated.', $message_arguments));
      $this->logger('pedido_entity')->notice('Updated new pedido %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.pedido.canonical', ['pedido' => $entity->id()]);
  }

}
