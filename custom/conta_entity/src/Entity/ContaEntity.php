<?php

namespace Drupal\conta_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\conta_entity\ContaEntityInterface;
use Drupal\user\UserInterface;

/**
 * Defines the conta entity class.
 *
 * @ContentEntityType(
 *   id = "conta_entity",
 *   label = @Translation("Conta"),
 *   label_collection = @Translation("Contas"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\conta_entity\ContaEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\conta_entity\ContaEntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\conta_entity\Form\ContaEntityForm",
 *       "edit" = "Drupal\conta_entity\Form\ContaEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "conta_entity",
 *   data_table = "conta_entity_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer conta",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/conta-entity/add",
 *     "canonical" = "/conta_entity/{conta_entity}",
 *     "edit-form" = "/admin/content/conta-entity/{conta_entity}/edit",
 *     "delete-form" = "/admin/content/conta-entity/{conta_entity}/delete",
 *     "collection" = "/admin/content/conta-entity"
 *   },
 *   field_ui_base_route = "entity.conta_entity.settings"
 * )
 */
class ContaEntity extends ContentEntityBase implements ContaEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new conta entity is created, set the uid entity reference to
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

    $fields['tipo_de_pagamento'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Selecione o tipo de pagamento :'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'allowed_values' => [
          'dinheiro' => 'Dinheiro',
          'credito' => 'Crédito',
          'debito' => 'Débito',
        ],
      ])
      ->setDefaultValue('Dinheiro')
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

    $fields['valor_pago'] = BaseFieldDefinition::create('decimal')
      ->setTranslatable(TRUE)
      ->setLabel(t('Valor pago pelo cliente ( R$ ) : '))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'decimal',
        'scale' => 2,
        'label' => 'above',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' =>'decimal',
        'scale' => 2,
        'label' => 'above',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setDescription(t('The user ID of the conta author.'))
      ->setSetting('target_type', 'user')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the conta was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the conta was last edited.'));

    return $fields;
  }

}
