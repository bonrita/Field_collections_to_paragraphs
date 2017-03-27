<?php

namespace Drupal\brt_migration\Plugin\migrate;

use Drupal\migrate\Plugin\migrate\destination\EntityConfigBase;
use Drupal\migrate\Row;

abstract class ParagraphConfigDestination extends EntityConfigBase {

  /**
   * Create or add fields to the bundle.
   *
   * Only create field if it does not exist on the Entity type.
   *
   * @param Row $row
   */
  protected abstract function CreateAddFields(Row $row);

  /**
   * Create custom view modes.
   *
   * Only created if it does not exist on the entity type already.
   *
   * @param Row $row
   */
  protected abstract function CreateViewModes(Row $row);

  /**
   * Add fields to the form display.
   *
   * @param Row $row
   */
  protected abstract function AddFormDisplay(Row $row);

  /**
   * Add view displays on the bundle.
   *
   * @param Row $row
   */
  protected abstract function AddViewDisplays(Row $row);

}
