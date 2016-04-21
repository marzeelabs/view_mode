<?php

/**
 * @file
 * Contains \Drupal\view_mode\Plugin\Field\FieldType\ListViewModeItem.
 */

namespace Drupal\view_mode\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;

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

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $options = $this->getOptions();

    foreach ($this->getSetting('view_modes') as $view_mode => $status) {
      if (!$status) {
        unset($options[$view_mode]);
      }
    }

    return $options;    
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return array_keys($this->getPossibleOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return $this->getPossibleValues($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    return $this->getPossibleOptions($account);
  }

  // @todo add support for entity_type
  protected function getOptions($entity_type = 'node') {
    $entity_manager = \Drupal::entityManager();
    $entity_type = $this->getEntity()->getEntityType()->get('id');

    $view_modes_info = $entity_manager->getViewModes($entity_type);

    $options = array();
    foreach ($view_modes_info as $view_mode_name => $view_mode_info) {
      $options[$view_mode_name] = $view_mode_info['label'];
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
      'view_modes' => array(),
    ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {

    $element = array();

    // @todo make this entity type agnostic
    $entity_type = 'node';

    $element['view_modes'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Enabled view modes'),
      '#description' => t('Select the view modes that can be selected for this field.'),
      '#default_value' => $this->getSetting('view_modes'),
      '#options' => $this->getOptions($entity_type),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function fieldSettingsToConfigData(array $settings)
  {
    foreach ($settings['view_modes'] as $key => $status) {
      if (!$status) {
        unset($settings['view_modes'][$key]);
      }
    }
    return $settings;
  }
}
