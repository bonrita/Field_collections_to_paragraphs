<?php

namespace Drupal\brt_migration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ctools\Plugin\Condition\NodeType;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\Migration;
use Drupal\migrate_tools\DrushLogMigrateMessage;
use Drupal\migrate_tools\MigrateExecutable;
use Drupal\Component\Utility\Unicode;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\migrate_plus\Entity\MigrationGroup;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Component\Serialization\Json;

/**
 * Class EloquaFormController.
 *
 * @package Drupal\chep_forms\Controller
 */
class EloquaFormController extends ControllerBase {

  /**
   * Form controller.
   *
   * @return array
   *   Render array.
   */
  public function pageForm() {

//    //-------------------------
    $migration_id = 'field_collections_all';
    $this->migrateFieldCollectionToParagraph($migration_id);
//    //----------------------------
    // ON POST IMPORT DELETE FIELD
//    $this->delete_field_from_node_bundles();
    //---------------------------


//    // Switch to external database
//    \Drupal\Core\Database\Database::setActiveConnection('migrate_brt');
//    // Get a connection going
//    $db = \Drupal\Core\Database\Database::getConnection();
//
//    $entity = \Drupal::entityTypeManager()->getStorage('field_storage_config')->load('node.field__a02_intro_text');
//
//    $bundles = $entity->getBundles();
//    // Switch back
//    \Drupal\Core\Database\Database::setActiveConnection();


//    $entities = \Drupal::entityTypeManager()->getListBuilder('field_storage_config')->load();
//    $storage = \Drupal::entityTypeManager()->getListBuilder('field_storage_config')->getStorage();
//    $entities = $storage->loadMultiple(['node.field__a02_intro_text']);



    $gg = 0;
    return [
      '#markup' => 'Test migrations',
    ];
  }

  public function brt_migrate_tools_migrate_reset_status($migration_id = '') {
    /** @var MigrationInterface $migration */
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    if ($migration) {
      $status = $migration->getStatus();
      if ($status == MigrationInterface::STATUS_IDLE) {
        drupal_set_message("Migration $migration_id is already Idle", 'warning', true);
      }
      else {
        $migration->setStatus(MigrationInterface::STATUS_IDLE);
        drupal_set_message("Migration $migration_id reset to Idle", 'status', true);
      }
    }
    else {
      drupal_set_message("Migration $migration_id does not exist", 'error', true);
    }
  }

  /**
   * @param $migration_id
   */
  public function migrateFieldCollectionToParagraph($migration_id) {
    $log = new MigrateMessage();
    $manager = \Drupal::service('plugin.manager.config_entity_migration');
    $plugins = $manager->createInstances([]);
    /** @var Migration $migration */
    $migration = $plugins[$migration_id];
    $executable = new MigrateExecutable($migration, $log, []);
    $executable->import();

    // Reset active migrationm
    $this->brt_migrate_tools_migrate_reset_status($migration_id);

  }

  public function CreateField() {
    $field_name = 'test_bona_table';
    $field_storage = FieldStorageConfig::loadByName('paragraph', $field_name);

    if (!$field_storage) {
      $field_storage_definition = array(
        'field_name' => $field_name,
        'entity_type' => 'paragraph',
        'type' => 'string',
        'cardinality' => 1,
        'translatable' => true,
        'settings' => array(
          'max_length' => 255,
        )
      );
      $field_storage = FieldStorageConfig::create($field_storage_definition);
      $field_storage->save();
    }
    $field = FieldConfig::loadByName('paragraph', 'a02_intro_text', $field_name);
    if ($field_storage && !$field) {
      $field_definition = array(
        'field_storage' => $field_storage,
        'bundle' => 'a02_intro_text',
        'label' => 'Title',
        'description' => '',
        'required' => true,
        'translatable' => true,
        'default_value' => [],
        'settings' => [],
      );
      $field = FieldConfig::create($field_definition);
      $field->save();
    }
  }

  protected function delete_field_from_node_bundles() {
    /** @var FieldStorageConfig $entity */
    $entity = \Drupal::entityTypeManager()->getStorage('field_storage_config')->load('node.field__a02_intro_text');

    $bundles = $entity->getBundles();


//    foreach ($bundles as $bundle) {
//      $id = "node.$bundle.field__a02_intro_text";
//      $field_config = \Drupal::entityTypeManager()->getStorage('field_config')->load($id);
//      $field_config->delete();
//      field_purge_field($field_config);
//    }
  }

}
