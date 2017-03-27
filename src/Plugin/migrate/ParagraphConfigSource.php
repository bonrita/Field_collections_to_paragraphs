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
  protected $paragraphId;

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
   */
  protected abstract function generateParagraphTypeBaseFields(Row $row);

  /**
   * Retrieve non bas fields from the field_collection.
   *
   * The fields are the custom fields on the bundle.
   *
   * @param Row $row
   *   The row instance.
   */
  protected abstract function retrieveNonBaseFields(Row $row);

  /**
   * Add fields to the form display.
   *
   * @param Row $row
   *   The row instance.
   */
  protected abstract function retrieveFormDisplayFields(Row $row);

  /**
   * Add view displays.
   *
   * Get field_collection view displays.
   *
   * @param Row $row
   */
  protected abstract function retrieveViewDisplays(Row $row);

  /**
   * Get view modes.
   *
   * @param Row $row
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

}
