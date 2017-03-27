<?php

namespace Drupal\brt_migration\EventSubscriber;

use Drupal\migrate\Event\ImportAwareInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\Event\MigrateRollbackEvent;
use Drupal\migrate\Event\RollbackAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FieldCollectionToParagraph implements EventSubscriberInterface {
  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[MigrateEvents::PRE_IMPORT][] = ['preImport'];
    $events[MigrateEvents::POST_IMPORT][] = ['postImport'];
//    $events[MigrateEvents::PRE_ROLLBACK][] = ['preRollback'];
//    $events[MigrateEvents::POST_ROLLBACK][] = ['postRollback'];

    return $events;
  }

}