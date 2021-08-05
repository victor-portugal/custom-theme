<?php

namespace Drupal\conta_entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a conta entity type.
 */
interface ContaEntityInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the conta creation timestamp.
   *
   * @return int
   *   Creation timestamp of the conta.
   */
  public function getCreatedTime();

  /**
   * Sets the conta creation timestamp.
   *
   * @param int $timestamp
   *   The conta creation timestamp.
   *
   * @return \Drupal\conta_entity\ContaEntityInterface
   *   The called conta entity.
   */
  public function setCreatedTime($timestamp);

}
