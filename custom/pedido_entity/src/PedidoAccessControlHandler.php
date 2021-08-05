<?php

namespace Drupal\pedido_entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the pedido entity type.
 */
class PedidoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view pedido');

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ['edit pedido', 'administer pedido'], 'OR');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ['delete pedido', 'administer pedido'], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, ['create pedido', 'administer pedido'], 'OR');
  }

}
