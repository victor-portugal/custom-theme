<?php

namespace Drupal\pedido_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\pedido_entity\PedidoInterface;
use Drupal\user\UserInterface;

/**
 * Defines the pedido entity class.
 *
 * @ContentEntityType(
 *   id = "pedido",
 *   label = @Translation("Pedido"),
 *   label_collection = @Translation("Pedidos"),
 *   handlers = {
 *     "view_builder" = "Drupal\pedido_entity\PedidoViewBuilder",
 *     "list_builder" = "Drupal\pedido_entity\PedidoListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\pedido_entity\PedidoAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\pedido_entity\Form\PedidoForm",
 *       "edit" = "Drupal\pedido_entity\Form\PedidoForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "pedido",
 *   data_table = "pedido_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer pedido",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/pedido/add",
 *     "canonical" = "/pedido/{pedido}",
 *     "edit-form" = "/admin/content/pedido/{pedido}/edit",
 *     "delete-form" = "/admin/content/pedido/{pedido}/delete",
 *     "collection" = "/admin/content/pedido"
 *   },
 *   field_ui_base_route = "entity.pedido.settings"
 * )
 */
class Pedido extends ContentEntityBase implements PedidoInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new pedido entity is created, set the uid entity reference to
   * the current user as the creator of the entity.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += ['uid' => \Drupal::currentUser()->id()];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['nome_mesa'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Nome da mesa:'))
      ->setRequired(TRUE)
      ->setSetting('handler_settings', ['target_bundles' => ['mesa' => 'mesa']])
      ->setDisplayOptions('form', [
        'type' => 'content_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status_pedido'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status do Pedido:'))
      ->setRequired(TRUE)
      ->setDescription(t('Status do andamento do pedido'))
      ->setCardinality(1)
      ->setSettings([
        'allowed_values' => [
          'aberto' => 'Aberto', 
          'em_andamento' => 'Em andamento',
          'pronto' => 'Pronto',
          'entregue' => 'Entregue',
          'pago' => 'Pago',
        ],
      ])
      ->setDefaultValue('Aberto')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['observacao'] = BaseFieldDefinition::create('string_long')
      ->setTranslatable(TRUE)
      ->setLabel(t('Observações :'))
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_long',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string_long',
        'label' => 'above',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setDescription(t('The user ID of the pedido author.'))
      ->setSetting('target_type', 'user')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['edited_by'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Ultima edição feita por:'))
      ->setDescription(t('ID do usuário que editou por último'))
      ->setSetting('target_type', 'user')
      ->setDefaultValue(\Drupal::currentUser()->id())
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the pedido was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['valor_total'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Preço Total: '))
      ->setRequired(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 5
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' =>'string',
        'label' => 'above',
        'weight' => 5
        ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the pedido was last edited.'));

    return $fields;
  }

}
