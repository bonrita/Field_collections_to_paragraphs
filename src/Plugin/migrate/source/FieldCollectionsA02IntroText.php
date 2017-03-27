<?php

namespace Drupal\brt_migration\Plugin\migrate\source;


use Drupal\migrate\Row;
use Drupal\brt_migration\Plugin\migrate\ParagraphConfigSource;

/**
 * Drupal 8 field collections source from database.
 *
 * @MigrateSource(
 *   id = "field_collections_a02_intro_text"
 * )
 */
class FieldCollectionsA02IntroText extends ParagraphConfigSource {

  /**
   * @inheritDoc
   */
  public function query() {
    $query = $this->select('cache_config', 'cc')->fields('cc', array('cid', 'data'));

    $cid = "field_collection.field_collection.{$this->configuration['configuration_object']}";
    $query->condition('cc.cid', $cid);

    return $query;
  }

  /**
   * @inheritDoc
   */
  public function fields() {
    return [
      'id' => $this->t('The machine ID of the paragraph.'),
      'label' => $this->t('The label of the paragraph.'),
      'fields' => $this->t('Fields'),
      'form_display' => $this->t('Form display.'),
      'view_displays' => $this->t('View displays.'),
      'view_modes' => $this->t('The custom view modes on the bundle.'),
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // Step 1.
    $this->generateParagraphTypeBaseFields($row);

    // Step 2.
    $this->retrieveNonBaseFields($row);

    // Step 3
    $this->retrieveFormDisplayFields($row);

    // Step 4.
    $this->retrieveViewDisplays($row);

    // Step 5.
    $this->retrieveCustomBundleViewModes($row);

    return parent::prepareRow($row);
  }

  /**
   * @inheritDoc
   */
  public function getIds() {
    $ids['cid']['type'] = 'string';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  protected function generateParagraphTypeBaseFields(Row $row) {
    $serialized_data = $row->getSourceProperty('data');

    $data = unserialize($serialized_data);
    $this->paragraphId = str_replace('field__', '', $data['id']);
    $clean = str_replace('_', ' ', $this->paragraphId);
    $label = ucfirst($clean);
    $row->setSourceProperty('type', $this->paragraphId);
    $row->setSourceProperty('name', $label);
  }

  /**
   * {@inheritdoc}
   */
  protected function retrieveNonBaseFields(Row $row) {
    $fields = [];
    $cid = "field.field.field_collection_item.{$this->configuration['configuration_object']}.%";
    $query = $this->select('cache_config', 'cc')->fields('cc', ['cid', 'data']);
    $query->condition('cc.cid', $cid, 'LIKE');
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      $field_parts = explode('.', $result['cid']);
      $field_name = array_pop($field_parts);
      $data = unserialize($result['data']);
      $data['entity_type'] = 'paragraph';
      $data['bundle'] = $this->paragraphId;

      $this->removeUnWantedfields($data);

      $fields[$field_name]['instance'] = $data;

      // Get Storage settings.
      $field_config_storage = "field.storage.field_collection_item.{$field_name}";
      $query = $this->select('cache_config', 'cc')->fields('cc', ['data']);
      $result = $query->condition('cc.cid', $field_config_storage)->execute()->fetchCol();
      $result = reset($result);
      $data = unserialize($result);
      $data['entity_type'] = 'paragraph';

      $this->removeUnWantedfields($data);

      $fields[$field_name]['storage'] = $data;
    }

    $this->fieldsDoExist = !empty($fields);

    $row->setSourceProperty('fields', $fields);
  }

  /**
   * {@inheritdoc}
   */
  protected function retrieveFormDisplayFields(Row $row) {
    if ($this->fieldsDoExist) {
      $cid = "core.entity_form_display.field_collection_item.{$this->configuration['configuration_object']}.%";
      $query = $this->select('cache_config', 'cc')->fields('cc', ['cid', 'data']);
      $query->condition('cc.cid', $cid, 'LIKE');
      $results = $query->execute()->fetchAll();
      $data = [];
      foreach ($results as $result) {
        $data = unserialize($result['data']);
        $data['bundle'] = $this->paragraphId;
        $data['targetEntityType'] = 'paragraph';

        $this->removeUnWantedfields($data);

      }
      $row->setSourceProperty('form_display', $data);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function retrieveViewDisplays(Row $row) {

    if ($this->fieldsDoExist) {
      $view_displays = [];
      $cid = "core.entity_view_display.field_collection_item.{$this->configuration['configuration_object']}.%";

      $query = $this->select('cache_config', 'cc')->fields('cc', ['cid', 'data']);
      $query->condition('cc.cid', $cid, 'LIKE');
      $results = $query->execute()->fetchAll();

      foreach ($results as $result) {
        $data = unserialize($result['data']);
        $data['bundle'] = $this->paragraphId;
        $data['targetEntityType'] = 'paragraph';

        $this->removeUnWantedfields($data);

        $view_displays[$data['mode']] = $data;
        $this->view_modes[$data['mode']] = [];
      }

      $row->setSourceProperty('view_displays', $view_displays);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function retrieveCustomBundleViewModes(Row $row) {

    foreach ($this->view_modes as $view_mode => $value) {
      $cid = "core.entity_view_mode.field_collection_item.$view_mode";
      $query = $this->select('cache_config', 'cc')->fields('cc', ['data']);
      $query->condition('cc.cid', $cid);
      $data = $query->execute()->fetchAssoc();

      if ($data) {
        $data = unserialize($data['data']);
        $data['targetEntityType'] = 'paragraph';
        $data['id'] = "{$data['targetEntityType']}.$view_mode";

        unset($data['uuid']);
        unset($data['langcode']);
        unset($data['dependencies']);
        unset($data['cache']);

        $this->view_modes[$view_mode] = $data;
      }

    }

    $this->view_modes = array_filter($this->view_modes);
    $row->setSourceProperty('view_modes', $this->view_modes);
  }

}

