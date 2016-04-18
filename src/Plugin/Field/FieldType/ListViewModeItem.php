<?php

/**
 * @file
 * Contains \Drupal\view_mode\Plugin\Field\FieldType\ListViewModeItem.
 */

namespace Drupal\view_mode\Plugin\Field\FieldType;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\DataDefinition;



// use Drupal\Component\Utility\Random;
// use Drupal\Core\Field\FieldDefinitionInterface;
// use Drupal\Core\Field\FieldItemBase;
// use Drupal\Core\Field\FieldStorageDefinitionInterface;


// use Drupal\Core\Form\FormStateInterface;
// use Drupal\Core\StringTranslation\TranslatableMarkup;
// use Drupal\Core\TypedData\DataDefinition;

use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\options\Plugin\Field\FieldType\ListStringItem;




/**
 * Plugin implementation of the 'list_view_mode' field type.
 *
 * @FieldType(
 *   id = "list_view_mode",
 *   label = @Translation("List (view mode)"),
 *   description = @Translation("This field stores view modes"),
 *   default_widget = "view_mode_select_widget",
 *   default_formatter = "view_mode_default_formatter"
 * )
 */
class ListViewModeItem extends FieldItemBase implements OptionsProviderInterface {

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'varchar',
          'length' => '255',
          'not null' => FALSE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('View mode'))
      ->addConstraint('Length', array('max' => 255))
      ->setRequired(TRUE);
    return $properties;
  }

  public function getPossibleOptions(AccountInterface $account = NULL) {


    return $this->getOptions($this->getEntity());

//    return array(
//      'vm1' => 'VM 1',
//      'vm2' => 'VM 2',
//    );
  }



  public function getPossibleValues(AccountInterface $account = NULL) {
    return $this->getValues($this->getEntity());
  }

  public function getSettableValues(AccountInterface $account = NULL) {
    return $this->getValues($this->getEntity());


//    // TODO: Implement getSettableValues() method.
//    return array(
//      'vm1',
//      'vm2',
//    );

  }

  public function getSettableOptions(AccountInterface $account = NULL) {

    return $this->getOptions($this->getEntity());
//    return $this->getPossibleOptions($account);

//    // TODO: Implement getSettableOptions() method.
//    return array(
//      'vm1' => 'VM 1',
//      'vm2' => 'VM 2',
//    );
  }

  protected function getValues(ContentEntityInterface $entity) {
    $options = $this->getOptions($entity);
    return array_keys($options);
  }

  protected function getOptions(ContentEntityInterface $entity) {
    $entity_manager = \Drupal::entityManager();
//    $entity_type = $this->getEntity()->getEntityType()->get('id');

    $entity_type = $entity->getEntityType()->get('id');

    $view_modes_info = $entity_manager->getViewModes($entity_type);

    $config_prefix = 'core.entity_view_display';
    $entity_type_id = $entity->getEntityType()->id();

    $ids = \Drupal::configFactory()->listAll($config_prefix . '.' . $entity_type_id . '.' . $entity->bundle() . '.');

    $load_ids = array();
    foreach ($ids as $id) {
      $config_id = str_replace($config_prefix . '.', '', $id);
      list(,, $display_mode) = explode('.', $config_id);
      $load_ids[] = $config_id;
    }

    dsm("HAHAH");

//    kint($view_modes_info);


    $enabled_display_modes = array();
    $displays = $entity_manager->getStorage('entity_view_display')->loadMultiple($load_ids);
    foreach ($displays as $display) {
      if ($display->status()) {
        kint($display);
        $enabled_display_modes[] = $display->get('mode');
      }
    }

//    kint($enabled_display_modes);

    $options = array();
    foreach ($view_modes_info as $view_mode_name => $view_mode_info) {
      $options[$view_mode_name] = $view_mode_info['label'];
    }
    return $options;


  }


}
