<?php

use CustomPostTypes\includes\classes\Utils;

add_filter(Utils::getHookName('field_types'), function ($fields) {
    $fields['tel'] = [
        'label' => __('Tel', 'custom-post-types'),
        'templateCallback' => function ($name, $id, $config) {
            return sprintf(
                '<input type="tel" name="%s" id="%s" value="%s" autocomplete="off" aria-autocomplete="none"%s%s>',
                $name,
                $id,
                $config['value'],
                !empty($config['extra']['placeholder']) ? ' placeholder="' . $config['extra']['placeholder'] . '"' : '',
                !empty($config['required']) ? ' required' : ''
            );
        },
        'extra' => [
            [ //placeholder
                'key' => 'placeholder',
                'label' => __('Placeholder', 'custom-post-types'),
                'info' => false,
                'required' => false,
                'type' => 'text',
                'extra' => [],
                'wrap' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                    'layout' => ''
                ]
            ]
        ],
        'sanitizeCallback' => function ($value) {
            return sanitize_text_field($value);
        }
    ];
    return $fields;
}, 80);