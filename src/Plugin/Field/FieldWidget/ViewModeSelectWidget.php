<?php

/**
 * @file
 * Contains \Drupal\view_mode\Plugin\Field\FieldWidget\ViewModeSelectWidget.
 */

namespace Drupal\view_mode\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;
use Drupal\Core\Entity\FieldableEntityInterface;


/**
 * Plugin implementation of the 'view_mode_select_widget' widget.
 *
 * @FieldWidget(
 *   id = "view_mode_select_widget",
 *   label = @Translation("Select list"),
 *   field_types = {
 *     "list_view_mode"
 *   }
 * )
 */
class ViewModeSelectWidget extends WidgetBase {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $element += array(
      '#type' => 'select',
      '#options' => $this->getOptions($items->getEntity()),
      '#default_value' => $value,
//      '#size' => 7,
//      '#maxlength' => 7,
//      '#element_validate' => array(
//        array($this, 'validate'),
//      ),
    );



//    $element += array(
//      '#type' => 'select',
//      '#options' => $this->getOptions($items->getEntity()),
//      '#default_value' => $this->getSelectedOptions($items),
//      // Do not display a 'multiple' select box if there is only one option.
//      '#multiple' => $this->multiple && count($this->options) > 1,
//    );


    return array('value' => $element);
  }

  /**
   * Validate the color text field.
   */
  public function validate($element, FormStateInterface $form_state) {
//    $value = $element['#value'];
//    if (strlen($value) == 0) {
//      $form_state->setValueForElement($element, '');
//      return;
//    }
  }

  /**
   * Returns the array of options for the widget.
   *
   * @return array
   *   The array of options for the widget.
   */
  protected function getOptions(FieldableEntityInterface $entity) {
//    return array(
//      'vm1' => 'VM 1',
//      'vm2' => 'VM 2',
//    );

//    if (!isset($this->options)) {

      // Limit the settable options for the current user account.
      $options = $this->fieldDefinition
        ->getFieldStorageDefinition()
//        ->getOptionsProvider($this->column, $entity)
        ->getOptionsProvider('value', $entity)
        ->getSettableOptions();
//
      return $options;
//
////      $this->options = $options;
////    }
//    return $this->options;
  }





}
