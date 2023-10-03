<?php

use CustomPostTypes\includes\classes\Utils;

add_filter(Utils::getHookName('field_types'), function ($fields) {
    $fields['color'] = [
        'label' => __('Color picker', 'custom-post-types'),
        'templateCallback' => function ($name, $id, $config) {
            return sprintf(
                '<div class="cpt-color-section"><input type="text" name="%s" id="%s" value="%s" autocomplete="off" aria-autocomplete="none"%s%s></div>',
                $name,
                $id,
                $config['value'],
                !empty($config['extra']['alpha']) && $config['extra']['alpha'] == 'true' ? ' data-alpha-enabled="true" data-alpha-color-type="hex"' : '',
                !empty($config['required']) ? ' required' : ''
            );
        },
        'extra' => [
            [ //alpha
                'key' => 'alpha',
                'label' => __('Alpha color', 'custom-post-types'),
                'info' => false,
                'required' => false,
                'type' => 'select',
                'extra' => [
                    'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                    'multiple' => false,
                    'options' => [
                        'true' => __('YES', 'custom-post-types'),
                        'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                    ]
                ],
                'wrap' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                    'layout' => ''
                ]
            ],
            ],
        'sanitizeCallback' => function ($value) {
            return sanitize_text_field($value);
        }
    ];
    return $fields;
}, 120);