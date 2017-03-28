<?php

namespace Drupal\brt_migration\Plugin\migrate;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;

abstract class ParagraphConfigSource extends DrupalSqlBase {

  /**
   * The machine ID of the paragraph entity.
   *
   * @var string
   */
  protected $paragraph_id;

  /**
   * Do fields exist on the source field_collection_item entity.
   *
   * @var bool
   */
  protected $fieldsDoExist;

  /**
   * Holds existing custom view modes on the paragraph.
   *
   * @var array
   */
  protected $view_modes;

  /**
   * @inheritDoc
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityManagerInterface $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_manager);

    $this->view_modes = [];
  }

  /**
   * Add entity type (paragraph_type) compulsory base fields.
   *
   * Convert fields from the field collection.
   *
   * @param Row $row
   *   The row instance.
   * @param string $paragraph_id
   *   The paragraph machine id.
   */
  protected abstract function generateParagraphTypeBaseFields(Row $row, $paragraph_id);

  /**
   * Retrieve non bas fields from the field_collection.
   *
   * The fields are the custom fields on the bundle.
   *
   * @param Row $row
   *   The row instance.
   * @param string $paragraph_id
   *   The paragraph machine id.
   * @param string $field_collection_machine_id
   *   The field collection machine id.
   */
  protected abstract function retrieveNonBaseFields(Row $row, $paragraph_id, $field_collection_machine_id);

  /**
   * Add fields to the form display.
   *
   * @param Row $row
   *   The row instance.
   * @param string $paragraph_id
   *   The paragraph machine id.
   * @param array $fields
   *   The list of fields on the field collection.
   * @param string $field_collection_machine_id
   *   The field collection machine id.
   */
  protected abstract function retrieveFormDisplayFields(Row $row, $paragraph_id, $fields, $field_collection_machine_id);

  /**
   * Add view displays.
   *
   * Get field_collection view displays.
   *
   * @param Row $row
   *   The row instance.
   * @param string $paragraph_id
   *   The paragraph machine id.
   * @param array $fields
   *   The list of fields on the field collection.
   * @param string $field_collection_machine_id
   *   The field collection machine id.
   */
  protected abstract function retrieveViewDisplays(Row $row, $paragraph_id, $fields, $field_collection_machine_id);

  /**
   * Get view modes.
   *
   * @param Row $row
   *   The row instance.
   */
  protected abstract function retrieveCustomBundleViewModes(Row $row);

  /**
   * Remove all unwanted fields.
   *
   * @param array $data
   *   The list of data items.
   */
  protected function removeUnWantedfields(&$data) {
    unset($data['uuid']);
    unset($data['langcode']);
    unset($data['dependencies']);
    unset($data['id']);
  }

  /**
   * Generate paragraph id.
   *
   * @param Row $row
   *   The row instance.
   *
   * @return  string
   *   The paragraph machine id.
   */
  protected function generateParagraphId(Row $row) {
    $field_collection_name = $this->getCurrentFieldCollectionMachineName($row);

    $paragraph_id = str_replace('field_', '', $field_collection_name);
    $paragraph_id = trim($paragraph_id, '_');
    return $paragraph_id;
  }

  /**
   * Get current field collection machine name.
   *
   * @param Row $row
   *    The row instance.
   *
   * @return string
   *   The field collection machine name.
   */
  protected function getCurrentFieldCollectionMachineName(Row $row) {
    $cid = $row->getSourceProperty('cid');
    $cid_parts = explode('.', $cid);
    $field_collection_name = array_pop($cid_parts);
    return $field_collection_name;
  }

}
