<?php

namespace Drupal\jsonapi_extras_text_format\Plugin\jsonapi\FieldEnhancer;

use Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerBase;
use Shaper\Util\Context;

/**
 * Provide a text format default if text format is not defined.
 *
 * @ResourceFieldEnhancer(
 *   id = "text_format_default",
 *   label = @Translation("Text format default"),
 *   description = @Translation("Provide a text format default if text format is not defined.")
 * )
 */
class TextFormatDefaultEnhancer extends ResourceFieldEnhancerBase {

  /**
   * {@inheritdoc}
   */
  public function getOutputJsonSchema() {
    return [
      'type' => 'object',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'text_format' => '',
    ];
  }

  /**
   * Get default text format.
   *
   * @return string
   *   Default text format.
   */
  public function getDefaultTextFormat() {
    $configuration = $this->getConfiguration();
    return $configuration['text_format'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $resource_field_info) {
    $settings = empty($resource_field_info['enhancer']['settings'])
      ? $this->getConfiguration()
      : $resource_field_info['enhancer']['settings'];

    $form = [];
    $form['text_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Default text format'),
      '#default_value' => $settings['text_format'],
      '#options' => $this->getAvailableFilterFormats(),
    ];

    return $form;
  }

  /**
   * Get all filter formats.
   *
   * @NOTE: should we filter by current user available formats?
   *
   * @return array
   *   List of formats as format_id => label.
   */
  public function getAvailableFilterFormats() {
    $filters = filter_formats();
    $filter_options = [];
    foreach ($filters as $filter) {
      $filter_options[$filter->id()] = $filter->label();
    }
    return $filter_options;
  }

  /**
   * {@inheritdoc}
   */
  protected function doTransform($data, Context $context) {
    if (is_string($data)) {
      $return = [
        'value'  => $data,
        'format' => $this->getDefaultTextFormat(),
      ];
    }
    elseif (is_array($data) && empty($data['format'])) {
      $return = $data + [
        'format' => $this->getDefaultTextFormat(),
      ];
    }
    else {
      $return = $data;
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  protected function doUndoTransform($data, Context $context) {
    return $data;
  }

}
