<?php

namespace Drupal\emulsify_twig;

use Drupal\Core\Template\Attribute;
use Drupal\Core\Template\TwigExtension;
use Twig\TwigFunction;

/**
 * Class DefaultService
 *
 * @package Drupal\EmulsifyExt
 */
class AddAttributesTwigExtension extends TwigExtension {
  /**
   * In this function we can declare the extension function.
   */
  public function getFunctions() {
    return array(
      new TwigFunction('add_attributes', array($this, 'add_attributes'), array('needs_context' => true, 'is_safe' => array('html'))),
    );
  }

  /*
   * This function is used to return alt of an image
   * Set image title as alt.
   */
  public function add_attributes($context, $additional_attributes = []) {
    $attributes = new Attribute();

    if (!empty($additional_attributes)) {
      foreach ($additional_attributes as $key => $value) {

        switch (gettype($value)) {
          case 'array':
            foreach ($value as $index => $item) {
              // Handle bem() output.
              if ($item instanceof Attribute) {
                // Remove the item.
                unset($value[$index]);
                $value = array_merge($value, $item->toArray()[$key]);
              }
            }
            break;

          case 'integer':
          case 'boolean':
          case 'string':
            // Handle bem() output.
            if ($value instanceof Attribute) {
              $value = $value->toArray()[$key];
            }
            else {
              $value = [strval($value)];
            }
            break;

          case 'object':
            if ($value instanceof Attribute) {
              $value = $value->toArray()[$key];
            }
            break;

          default:
            // Set value to an empty string.
            $value = '';
            break;
        }
        // Merge additional attribute values with existing ones.
        if ($context['attributes']->offsetExists($key)) {
          $existing_attribute = $context['attributes']->offsetGet($key)->value();
          $value = array_merge($existing_attribute, $value);
        }
        $context['attributes']->setAttribute($key, $value);
      }
    }

    // Set all attributes.
    foreach($context['attributes'] as $key => $value) {
      $attributes->setAttribute($key, $value);
      // Remove this attribute from context so it doesn't filter down to child
      // elements.
      $context['attributes']->removeAttribute($key);
    }

    return $attributes;
  }
}
