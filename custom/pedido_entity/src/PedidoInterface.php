<?php

namespace Drupal\pedido_entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a pedido entity type.
 */
interface PedidoInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the pedido creation timestamp.
   *
   * @return int
   *   Creation timestamp of the pedido.
   */
  public function getCreatedTime();

  /**
   * Sets the pedido creation timestamp.
   *
   * @param int $timestamp
   *   The pedido creation timestamp.
   *
   * @return \Drupal\pedido_entity\PedidoInterface
   *   The called pedido entity.
   */
  public function setCreatedTime($timestamp);

}
