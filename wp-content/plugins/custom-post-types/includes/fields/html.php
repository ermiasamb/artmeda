<?php

use CustomPostTypes\includes\classes\Utils;

add_filter(Utils::getHookName('field_types'), function ($fields) {
    $fields['html'] = [
        'label' => __('Html', 'custom-post-types'),
        'templateCallback' => function ($name, $id, $config) {
            return !empty($config['extra']['content']) ? $config['extra']['content'] : '';
        },
        'extra' => [
            [ //content
                'key' => 'content',
                'label' => __('Content', 'custom-post-types'),
                'info' => false,
                'required' => false,
                'type' => 'tinymce',
                'extra' => [],
                'wrap' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                    'layout' => ''
                ]
            ],
        ]
    ];
    return $fields;
}, 160);