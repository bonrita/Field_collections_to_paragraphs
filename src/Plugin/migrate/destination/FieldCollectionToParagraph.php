<?php

namespace Drupal\brt_migration\Plugin\migrate\destination;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\migrate\Row;
use Drupal\brt_migration\Plugin\migrate\ParagraphConfigDestination;

/**
 * This class converts one field collection item bundle to a paragraph bundle.
 *
 * @MigrateDestination(
 *   id = "entity:paragraphs_type"
 * )
 */
class FieldCollectionToParagraph extends ParagraphConfigDestination {

  /**
   * The entity id.
   *
   * @var string
   */
  protected $entityId;

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = array()) {
    $entity_ids = parent::import($row, $old_destination_id_values);

    if (!empty($entity_ids)) {
      $this->entityId = reset($entity_ids);

      // Step 1.
      $this->CreateAddFields($row);

      // Step 2.
      $this->CreateViewModes($row);

      // Step 3.
      $this->AddFormDisplay($row);

      // Step 4.
      $this->AddViewDisplays($row);

    }

    return $entity_ids;
  }

  /**
   * {@inheritdoc}
   */
  protected function CreateAddFields(Row $row) {
    $fields = $row->getSourceProperty('fields');
    foreach ($fields as $field_name => $field_data) {

      // Get the field storage.
      $field_storage = FieldStorageConfig::loadByName('paragraph', $field_name);

      if (!$field_storage) {
        $field_storage = FieldStorageConfig::create($field_data['storage']);
        $field_storage->save();
      }

      // Get the field instance configurations.
      $field = FieldConfig::loadByName('paragraph', $this->entityId, $field_name);
      if ($field_storage && !$field) {
        $field_data['instance']['field_storage'] = $field_storage;
        $field = FieldConfig::create($field_data['instance']);
        $field->save();
      }

    }
  }

  /**
   * {@inheritdoc}
   */
  protected function CreateViewModes(Row $row) {
    $view_modes = $row->getSourceProperty('view_modes');
    foreach ($view_modes as $view_mode_id => $view_mode_data) {
      $view_mode = EntityViewMode::load($view_mode_data['id']);
      if (!$view_mode) {
        $view_mode = EntityViewMode::create($view_mode_data);
        $view_mode->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function AddFormDisplay(Row $row) {
    $form_display_data = $row->getSourceProperty('form_display');
    $form_display = EntityFormDisplay::create($form_display_data);
    $form_display->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function AddViewDisplays(Row $row) {
    $view_display_data = $row->getSourceProperty('view_displays');
    foreach ($view_display_data as $display_data) {
      $view_display = EntityViewDisplay::create($display_data);
      $view_display->save();
    }
  }

  /**
   * Drupal\migrate\Event\ImportAwareInterface
   * Implementing the above interface in this plugin will make the migrate module call the method "preimport"
   * @see core/modules/migrate/src/Plugin/PluginEventSubscriber.php :: invoke() line 37
   */

}
