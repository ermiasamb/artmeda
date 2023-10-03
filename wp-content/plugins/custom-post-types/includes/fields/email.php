<?php

use CustomPostTypes\includes\classes\Utils;

add_filter(Utils::getHookName('field_types'), function ($fields) {
    $fields['email'] = [
        'label' => __('Email', 'custom-post-types'),
        'templateCallback' => function ($name, $id, $config) {
            return sprintf(
                '<div style="display: none;"><input type="email" name="%s"></div><input type="email" name="%s" id="%s" value="%s" autocomplete="off" aria-autocomplete="none"%s%s>',
                $name,
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
            return sanitize_email($value);
        }
    ];
    return $fields;
}, 90);

// TODO: a cosa serve?
add_filter(Utils::getHookName('sanitize_field_email'), function ($value) {
    return sanitize_text_field($value);
});