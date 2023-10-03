<?php

namespace CustomPostTypes\includes\classes;

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

class FieldGroups
{
    /**
     * @var array
     */
    public $screensWithFields = [];

    /**
     * @var array
     */
    public $uiRegistrationArgsTitleField = [];

    /**
     * @var array
     */
    public $uiRegistrationLabelsTitleField = [];

    /**
     * @var array
     */
    public $uiRegistrationViewSwitchField = [];

    public function __construct()
    {
        $this->uiRegistrationArgsTitleField = [
            'key' => 'args_title',
            'label' => '',
            'info' => '',
            'required' => false,
            'type' => 'html',
            'extra' => [
                'content' => sprintf(
                    '<h2>%s</h2>',
                    __('Registration args', 'custom-post-types')
                )
            ],
            'wrap' => [
                'width' => '',
                'class' => 'advanced-field',
                'id' => '',
                'layout' => ''
            ]
        ];
        $this->uiRegistrationLabelsTitleField = [
            'key' => 'labels_title',
            'label' => '',
            'info' => '',
            'required' => false,
            'type' => 'html',
            'extra' => [
                'content' => sprintf(
                    '<h2>%s</h2>',
                    __('Registration label', 'custom-post-types')
                )
            ],
            'wrap' => [
                'width' => '',
                'class' => 'advanced-field',
                'id' => '',
                'layout' => ''
            ]
        ];
        $this->uiRegistrationViewSwitchField = [
            'key' => 'advanced_fields',
            'label' => '',
            'info' => '',
            'required' => false,
            'type' => 'html',
            'extra' => [
                'content' => sprintf(
                    '<button class="button button-primary"><span class="dashicons dashicons-insert"></span><span class="label">%s</span></button>',
                    __('Advanced view', 'custom-post-types')
                )
            ],
            'wrap' => [
                'width' => '',
                'class' => 'advanced-field-btn',
                'id' => '',
            ]
        ];
    }

    /**
     * @param $optionsString
     * @return array
     */
    private function getOptionsFromString($optionsString = '')
    {
        $rows = explode(PHP_EOL, $optionsString);
        $optionsArray = [];
        foreach ($rows as $row) {
            if (strpos($row, '|') !== false) {
                $optionsArray[trim(explode('|', $row)[0])] = trim(explode('|', $row)[1]);
            } else {
                $optionsArray[trim($row)] = trim($row);
            }
        }
        return $optionsArray;
    }

    /**
     * @param $field
     * @return array
     */
    private function sanitizeFieldArgs($field = [])
    {
        $field['required'] = !empty($field['required']) && $field['required'] == 'true';
        if (!empty($field['extra']['options']) && !is_array($field['extra']['options'])) {
            $field['extra']['options'] = $this->getOptionsFromString($field['extra']['options']);
        }
        foreach ($field as $key => $value) {
            if (substr($key, 0, 5) === "wrap_") {
                if (!empty($value)) {
                    $field['wrap'][str_replace("wrap_", "", $key)] = $value;
                }
                unset($field[$key]);
            }
        }
        return $field;
    }

    /**
     * @return array
     */
    private function getRegisteredGroups()
    {
        $registeredGroups = get_posts([
            'posts_per_page' => -1,
            'post_type' => Utils::getInfo('ui_prefix') . '_field',
            'post_status' => 'publish'
        ]);

        $groupsByUi = [];

        foreach ($registeredGroups as $group) {
            $groupId = sanitize_title($group->post_title);
            $groupLabel = $group->post_title;
            $groupSupports = !empty(get_post_meta($group->ID, 'supports', true)) ? array_map(
                function ($support) {
                    $contentType = 'cpt';
                    $contentId = $support;
                    if (strpos($support, '/') !== false) {
                        $contentType = explode('/', $support)[0];
                        $contentId = explode('/', $support)[1];
                    }
                    return [
                        'type' => $contentType,
                        'id' => $contentId,
                    ];
                },
                get_post_meta($group->ID, 'supports', true)
            ) : [];
            $groupPosition = !empty(get_post_meta($group->ID, 'position', true)) ? get_post_meta($group->ID, 'position', true) : 'normal';
            $groupOrder = get_post_meta($group->ID, 'order', true);
            $groupAdminOnly = get_post_meta($group->ID, 'admin_only', true) == 'true';
            $groupShowInRest = get_post_meta($group->ID, 'show_in_rest', true) == 'true';
            $groupFields = !empty(get_post_meta($group->ID, 'fields', true)) ? array_map(
                function ($field) {
                    return $this->sanitizeFieldArgs($field);
                },
                get_post_meta($group->ID, 'fields', true)
            ) : [];

            $groupsByUi[] = [
                'id' => $groupId,
                'label' => $groupLabel,
                'supports' => $groupSupports,
                'position' => $groupPosition,
                'order' => $groupOrder,
                'admin_only' => $groupAdminOnly,
                'show_in_rest' => $groupShowInRest,
                'fields' => $groupFields,
            ];
        }

        unset($registeredGroups);

        return (array)apply_filters(Utils::getHookName('register_fields'), $groupsByUi);
    }

    /**
     * @param $key
     * @param $parent
     * @return string|void
     */
    private function getFieldInputName($key = '', $parent = false)
    {
        if (empty($key)) return;
        return "meta-fields" . ($parent ?: '') . '[' . $key . ']';
    }

    /**
     * @param $key
     * @param $parent
     * @return string|void
     */
    private function getFieldInputId($key = '', $parent = false)
    {
        if (empty($key)) return;
        $parent = $parent ? str_replace('][', '-', $parent) : '';
        $parent = str_replace('[', '-', $parent);
        $parent = str_replace(']', '', $parent);
        return "meta-fields" . $parent . '-' . $key;
    }

    /**
     * @param $fieldConfig
     * @return string|void
     */
    public function getFieldTemplate($fieldConfig = [])
    {
        $parent = !empty($fieldConfig['parent']) ? $fieldConfig['parent'] : false;
        $fieldId = $this->getFieldInputId($fieldConfig['key'], $parent);
        $fieldName = $this->getFieldInputName($fieldConfig['key'], $parent);
        $fieldTemplateCallback = $this->getAvailableFieldTemplateCallback($fieldConfig['type']);
        if (!$fieldTemplateCallback) {
            return;
        }
        ob_start();
        ?>
        <div
            class="cpt-field"<?php echo !empty($fieldConfig['wrap']['width']) ? ' style="width: ' . $fieldConfig['wrap']['width'] . '%"' : ''; ?>
            data-field-type="<?php echo $fieldConfig['type']; ?>">
            <div class="cpt-field-inner">
                <input type="hidden" name="<?php echo $fieldName; ?>" value="">
                <?php printf(
                    '<div class="cpt-field-wrap%s"%s><label for="%s">%s</label><div class="input">%s</div>%s</div>',
                    (!empty($fieldConfig['wrap']['layout']) ? ' ' . $fieldConfig['wrap']['layout'] : '') .
                    ($fieldConfig['required'] ? ' cpt-field-required' : '') .
                    (!empty($fieldConfig['wrap']['class']) ? ' ' . $fieldConfig['wrap']['class'] : ''),
                    !empty($fieldConfig['wrap']['id']) ? ' id="' . $fieldConfig['wrap']['id'] . '"' : '',
                    $fieldId,
                    $fieldConfig['label'],
                    $fieldTemplateCallback($fieldName, $fieldId, $fieldConfig),
                    !empty($fieldConfig['info']) ? '<div class="description"><p>' . $fieldConfig['info'] . '</p></div>' : ''
                ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * @param $config
     * @param $getValueCallback
     * @return false|string
     */
    private function getFieldsSection($config = [], $getValueCallback = null)
    {
        $fields = !empty($config['fields']) ? $config['fields'] : [];
        ob_start();
        wp_nonce_field(Utils::getHookName('fields_nonce'), 'fields-nonce');
        ?>
        <div class="cpt-fields-section" data-id="<?php echo $config['id']; ?>">
            <?php
            foreach ($fields as $field) {
                $field['value'] = $getValueCallback($field['key']);
                $field = apply_filters(Utils::getHookName($field['type'] . '_field_args'), $field);
                echo $this->getFieldTemplate($field);
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * @param $fields
     * @param $saveValueCallback
     * @return void
     */
    private function saveMeta($fields = [], $saveValueCallback = null)
    {
        if (
            empty($_POST['fields-nonce']) ||
            !wp_verify_nonce($_POST['fields-nonce'], Utils::getHookName('fields_nonce')) ||
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        ) {
            return;
        }
        $metaValues = isset($_POST['meta-fields']) ? $_POST['meta-fields'] : [];

        foreach ($fields as $field) {
            $metaKey = $field['key'];
            $fieldSanitizeCallback = $this->getAvailableFieldSanitizeCallback($field['type']);
            if (!isset($metaValues[$metaKey])) {
                return;
            } elseif (!empty($metaValues[$metaKey])) {
                /*
                 * Sanitize using field registration callback
                 */
                $sanitizeValue = $fieldSanitizeCallback ? $fieldSanitizeCallback($metaValues[$metaKey]) : $metaValues[$metaKey];
            } else {
                $sanitizeValue = '';
            }
            if ($saveValueCallback) {
                $saveValueCallback($metaKey, $sanitizeValue);
            }
        }
    }

    /**
     * @param $taxonomy
     * @param $config
     * @return void
     */
    private function initTaxonomyFields($taxonomy = '', $config = [])
    {
        $this->initRestFields($taxonomy, $config, function ($key, $id) {
            return $this->getTermField($key, $id);
        });

        $addActions = [
            $taxonomy . '_add_form_fields',
            $taxonomy . '_edit_form'
        ];
        foreach ($addActions as $action) {
            add_action($action, function ($term) use ($config) {
                echo $this->getFieldsSection($config, function ($key) use ($term) {
                    return !empty($term->term_id) ? get_term_meta($term->term_id, $key, true) : null;
                });
            });
        }

        $saveActions = [
            'edited_' . $taxonomy,
            'created_' . $taxonomy
        ];
        foreach ($saveActions as $action) {
            add_action($action, function ($termId) use ($config, $taxonomy) {
                $this->saveMeta($config['fields'], function ($key, $value) use ($termId, $taxonomy) {
                    $value = $this->getSanitizedValue($taxonomy, $key, $value);
                    return update_term_meta($termId, $key, $value);
                });
            });
        }
    }

    /**
     * @param $postType
     * @param $config
     * @return void
     */
    private function initPostTypeFields($postType = '', $config = [])
    {
        $this->initRestFields($postType, $config, function ($key, $id) {
            return $this->getPostField($key, $id);
        });

        add_action('add_meta_boxes', function ($posttype) use ($postType, $config) {
            if ($posttype !== $postType) {
                return;
            }
            add_meta_box(
                $config['id'],
                $config['label'],
                function ($post) use ($config) {
                    echo $this->getFieldsSection($config, function ($key) use ($post) {
                        return !empty($post->ID) ? get_post_meta($post->ID, $key, true) : null;
                    });
                },
                $postType,
                $config['position']
            );
        });
        add_action('save_post_' . $postType, function ($post_id) use ($config, $postType) {
            $this->saveMeta($config['fields'], function ($key, $value) use ($post_id, $postType) {
                $value = $this->getSanitizedValue($postType, $key, $value);
                return update_post_meta($post_id, $key, $value);
            });
        });
    }

    /**
     * @param $optionsPage
     * @param $config
     * @return void
     */
    private function initOptionsPageFields($optionsPage = '', $config = [])
    {
        foreach ($config['fields'] as $i => $field) {
            $config['fields'][$i]['key'] = $optionsPage . '-' . $config['fields'][$i]['key'];
        }

        add_action('admin_init', function () use ($optionsPage, $config) {
            register_setting($optionsPage, 'meta-fields');
            add_settings_section($config['id'], $config['label'], function () use ($optionsPage, $config) {
                echo $this->getFieldsSection($config, function ($key) {
                    return get_option($key);
                });
            }, $optionsPage);
        });
        add_action('update_option_meta-fields', function () use ($optionsPage, $config) {
            $this->saveMeta($config['fields'], function ($key, $value) use ($optionsPage) {
                $value = $this->getSanitizedValue($optionsPage, $key, $value);
                return update_option($key, $value);
            });
        });
    }

    /**
     * @param $optionsPage
     * @param $config
     * @return void
     */
    private function initUserFields($config = [])
    {
        $this->initRestFields('user', $config, function ($key, $id) {
            return $this->getUserField($key, $id);
        });

        $addActions = [
            'show_user_profile',
            'edit_user_profile'
        ];
        foreach ($addActions as $action) {
            add_action($action, function ($user) use ($config) {
                echo $this->getFieldsSection($config, function ($key) use ($user) {
                    return !empty($user->ID) ? get_user_meta($user->ID, $key, true) : null;
                });
            });
        }

        $saveActions = [
            'personal_options_update',
            'edit_user_profile_update'
        ];
        foreach ($saveActions as $action) {
            add_action($action, function ($userId) use ($config) {
                $this->saveMeta($config['fields'], function ($key, $value) use ($userId) {
                    $value = $this->getSanitizedValue($userId, $key, $value);
                    return update_user_meta($userId, $key, $value);
                });
            });
        }
    }

    /**
     * @param $config
     * @return void
     */
    private function initMediaFields($config = [])
    {
        $this->initRestFields('attachment', $config, function ($key, $id) {
            return $this->getMediaField($key, $id);
        });

        add_action('add_meta_boxes', function ($posttype) use ($config) {
            if ($posttype !== 'attachment') {
                return;
            }
            add_meta_box(
                $config['id'],
                $config['label'],
                function ($post) use ($config) {
                    echo $this->getFieldsSection($config, function ($key) use ($post) {
                        return !empty($post->ID) ? get_post_meta($post->ID, $key, true) : null;
                    });
                },
                'attachment',
                $config['position']
            );
        });
        add_action('edit_attachment', function ($post_id) use ($config) {
            $this->saveMeta($config['fields'], function ($key, $value) use ($post_id) {
                $value = $this->getSanitizedValue('attachment', $key, $value);
                return update_post_meta($post_id, $key, $value);
            });
        });
    }

    /**
     * @param $config
     * @return void
     */
    private function initCommentFields($config = [])
    {
        $this->initRestFields('comment', $config, function ($key, $id) {
            return $this->geCommentField($key, $id);
        });

        add_action('add_meta_boxes', function ($posttype) use ($config) {
            if ($posttype !== 'comment') {
                return;
            }
            add_meta_box(
                $config['id'],
                $config['label'],
                function ($comment) use ($config) {
                    echo $this->getFieldsSection($config, function ($key) use ($comment) {
                        return !empty($comment->comment_ID) ? get_comment_meta($comment->comment_ID, $key, true) : null;
                    });
                },
                'comment',
                $config['position']
            );
        });
        add_action('edit_comment', function ($comment_id) use ($config) {
            $this->saveMeta($config['fields'], function ($key, $value) use ($comment_id) {
                $value = $this->getSanitizedValue('comment', $key, $value);
                return update_comment_meta($comment_id, $key, $value);
            });
        });
    }

    /**
     * @param $config
     * @return void
     */
    private function initMenuItemFields($config = [])
    {
        $this->initRestFields('nav_menu_item', $config, function ($key, $id) {
            return $this->geMenuItemField($key, $id);
        });

        add_action('wp_nav_menu_item_custom_fields', function ($item_id) use ($config) {
            foreach ($config['fields'] as $i => $field) {
                $config['fields'][$i]['key'] .= '-' . $item_id;
            }

            echo '<div class="description description-wide">';
            echo $this->getFieldsSection($config, function ($key) use ($item_id) {
                $key = str_replace('-' . $item_id, '', $key);
                return !empty($item_id) ? get_post_meta($item_id, $key, true) : null;
            });
            echo '</div>';
        });
        add_action('wp_update_nav_menu_item', function ($menu_id, $item_id) use ($config) {
            foreach ($config['fields'] as $i => $field) {
                $config['fields'][$i]['key'] = $item_id . $config['fields'][$i]['key'];
            }

            $this->saveMeta($config['fields'], function ($key, $value) use ($item_id) {
                $key = str_replace('-' . $item_id, '', $key);
                $value = $this->getSanitizedValue('menu-item', $key, $value);
                return update_post_meta($item_id, $key, $value);
            });
        }, 10, 2);
    }

    /**
     * @param $referenceId
     * @param $key
     * @param $value
     * @return mixed
     */
    private function getSanitizedValue($referenceId, $key, $value)
    {
        /*
         * Sanitize using field based filter
         */
        $value = apply_filters(Utils::getHookName('sanitize') . '_field_' . $key, $value);
        /*
         * Sanitize using reference based filter
         */
        $value = apply_filters(Utils::getHookName('sanitize') . '_' . $referenceId, $value);
        /*
         * Sanitize using reference and field based filter
         */
        return apply_filters(Utils::getHookName('sanitize') . '_' . $referenceId . '_field_' . $key, $value);
    }

    /**
     * @return void
     */
    public function initRegisteredGroups()
    {
        $fieldGroups = $this->getRegisteredGroups();

        foreach ($fieldGroups as $fieldGroup) {
            $id = !empty($fieldGroup['id']) && is_string($fieldGroup['id']) ? $fieldGroup['id'] : false;
            $supports = !empty($fieldGroup['supports']) && is_array($fieldGroup['supports']) ? $fieldGroup['supports'] : false;
            $label = !empty($fieldGroup['label']) ? $fieldGroup['label'] : false;
            $adminOnly = !empty($fieldGroup['admin_only']) ? $fieldGroup['admin_only'] : false;
            unset($fieldGroup['supports'], $fieldGroup['admin_only']);
            if (
                ($adminOnly && !current_user_can('administrator')) ||
                (!$adminOnly && !current_user_can('edit_posts'))
            ) {
                continue;
            }
            if (!$id || !$supports || !$label) {
                $errorInfo = Utils::getRegistrationErrorNoticeInfo($fieldGroup, 'field');

                add_filter(Utils::getHookName('register_notices'), function ($args) use ($errorInfo) {
                    $args[] = [
                        'id' => $errorInfo['id'],
                        'title' => Utils::getNoticesTitle(),
                        'message' => __('Field group registration was not successful ("id" "label" and "supports" args are required).', 'custom-post-types') . $errorInfo['details'],
                        'type' => 'error',
                        'dismissible' => 3,
                        'admin_only' => 'true',
                        'buttons' => false,
                    ];
                    return $args;
                });
                continue;
            }
            foreach ($supports as $content) {
                $type = !empty($content['type']) ? $content['type'] : 'cpt';
                $id = !empty($content['id']) ? $content['id'] : false;
                if (!$id) {
                    continue;
                }
                switch ($type) {
                    case 'cpt':
                        $this->screensWithFields[] = $id;
                        $this->initPostTypeFields($id, $fieldGroup);
                        break;
                    case 'tax':
                        $this->screensWithFields[] = 'edit-' . $id;
                        $this->initTaxonomyFields($id, $fieldGroup);
                        break;
                    case 'options':
                        $coreOptionsPages = Utils::getCoreSettingsPagesOptions();
                        if (isset($coreOptionsPages[$id])) {
                            $this->screensWithFields[] = 'options-' . $id;
                        } else {
                            $this->screensWithFields[] = '_page_' . $id;
                        }
                        $this->initOptionsPageFields($id, $fieldGroup);
                        break;
                    case 'extra':
                        if ($id == 'users') {
                            $this->screensWithFields[] = 'user-edit';
                            $this->screensWithFields[] = 'profile';
                            $this->initUserFields($fieldGroup);
                        }
                        if ($id == 'media') {
                            $this->screensWithFields[] = 'attachment';
                            $this->initMediaFields($fieldGroup);
                        }
                        if ($id == 'comments') {
                            $this->screensWithFields[] = 'comment';
                            $this->initCommentFields($fieldGroup);
                        }
                        if ($id == 'menu-items') {
                            $this->screensWithFields[] = 'nav-menus';
                            $this->initMenuItemFields($fieldGroup);
                        }
                        break;
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getAvailableFields()
    {
        return apply_filters(Utils::getHookName('field_types'), []);
    }

    /**
     * @return array
     */
    public function getAvailableFieldsLabel()
    {
        $options = [];
        $fieldTypes = $this->getAvailableFields();
        foreach ($fieldTypes as $fieldType => $args) {
            $options[$fieldType] = $args['label'];
        }
        unset($fieldTypes);
        return $options;
    }

    /**
     * @return array
     */
    public function getAvailableFieldsExtra()
    {
        $options = [];
        $fieldTypes = $this->getAvailableFields();
        foreach ($fieldTypes as $fieldType => $args) {
            $options[$fieldType] = !empty($args['extra']) ? $args['extra'] : [];
        }
        unset($fieldTypes);
        $options['repeater'] = [$this->getRepeaterFields()];
        return $options;
    }

    /**
     * @param $fieldType
     * @return false|mixed
     */
    private function getAvailableFieldTemplateCallback($fieldType = '')
    {
        $fieldTypes = $this->getAvailableFields();
        return
            !empty($fieldTypes[$fieldType]['templateCallback']) &&
            is_callable($fieldTypes[$fieldType]['templateCallback']) ?
                $fieldTypes[$fieldType]['templateCallback'] :
                false;
    }

    /**
     * @param $fieldType
     * @return false|mixed
     */
    private function getAvailableFieldSanitizeCallback($fieldType = '')
    {
        $fieldTypes = $this->getAvailableFields();
        return
            !empty($fieldTypes[$fieldType]['sanitizeCallback']) &&
            is_callable($fieldTypes[$fieldType]['sanitizeCallback']) ?
                $fieldTypes[$fieldType]['sanitizeCallback'] :
                false;
    }

    /**
     * @param $fieldType
     * @return false|mixed
     */
    public function getAvailableFieldGetCallback($fieldType = '')
    {
        $fieldTypes = $this->getAvailableFields();
        return
            !empty($fieldTypes[$fieldType]['getCallback']) &&
            is_callable($fieldTypes[$fieldType]['getCallback']) ?
                $fieldTypes[$fieldType]['getCallback'] :
                false;
    }

    /**
     * @return array
     */
    public function getRepeaterFields()
    {
        return [ // fields
            'key' => 'fields',
            'label' => __('Fields list', 'custom-post-types'),
            'info' => '',
            'required' => false,
            'type' => 'repeater',
            'extra' => [
                'fields' => [
                    [ //label
                        'key' => 'label',
                        'label' => __('Label', 'custom-post-types'),
                        'info' => false,
                        'required' => true,
                        'type' => 'text',
                        'extra' => [],
                        'wrap' => [
                            'width' => '40',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //key
                        'key' => 'key',
                        'label' => __('Key', 'custom-post-types'),
                        'info' => false,
                        'required' => true,
                        'type' => 'text',
                        'extra' => [],
                        'wrap' => [
                            'width' => '40',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //required
                        'key' => 'required',
                        'label' => __('Required', 'custom-post-types'),
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
                            'width' => '20',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //type
                        'key' => 'type',
                        'label' => __('Type', 'custom-post-types'),
                        'info' => false,
                        'required' => true,
                        'type' => 'select',
                        'extra' => [
                            'multiple' => false,
                            'options' => $this->getAvailableFieldsLabel(),
                        ],
                        'wrap' => [
                            'width' => '40',
                            'class' => 'cpt-repeater-field-type',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //info
                        'key' => 'info',
                        'label' => __('Info', 'custom-post-types'),
                        'info' => false,
                        'required' => false,
                        'type' => 'text',
                        'extra' => [],
                        'wrap' => [
                            'width' => '60',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //wrap_width
                        'key' => 'wrap_width',
                        'label' => __('Container width', 'custom-post-types') . ' (%)',
                        'info' => false,
                        'required' => false,
                        'type' => 'number',
                        'extra' => [
                            'placeholder' => '100',
                            'min' => 1,
                            'max' => 100
                        ],
                        'wrap' => [
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ],
                        'parent' => ''
                    ],
                    [ //wrap_layout
                        'key' => 'wrap_layout',
                        'label' => __('Container layout', 'custom-post-types'),
                        'info' => false,
                        'required' => false,
                        'type' => 'select',
                        'extra' => [
                            'placeholder' => __('VERTICAL', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                            'multiple' => false,
                            'options' => [
                                'vertical' => __('VERTICAL', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                                'horizontal' => __('HORIZONTAL', 'custom-post-types'),
                            ]
                        ],
                        'wrap' => [
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //wrap_class
                        'key' => 'wrap_class',
                        'label' => __('Container class', 'custom-post-types'),
                        'info' => false,
                        'required' => false,
                        'type' => 'text',
                        'extra' => [],
                        'wrap' => [
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                    [ //wrap_id
                        'key' => 'wrap_id',
                        'label' => __('Container id', 'custom-post-types'),
                        'info' => false,
                        'required' => false,
                        'type' => 'text',
                        'extra' => [],
                        'wrap' => [
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                            'layout' => ''
                        ]
                    ],
                ]
            ],
            'wrap' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ]
        ];
    }

    /**
     * @return array
     */
    public function getNewFieldGroupFields()
    {
        return [
            'id' => Utils::getInfo('ui_prefix') . '_field',
            'label' => __('Field group settings', 'custom-post-types'),
            'supports' => [[
                'type' => 'cpt',
                'id' => Utils::getInfo('ui_prefix') . '_field'
            ]],
            'position' => 'normal',
            'order' => 0,
            'admin_only' => true,
            'fields' => [
                [ //position
                    'key' => 'position',
                    'label' => __('Position', 'custom-post-types'),
                    'info' => __('If set to "NORMAL" it will be shown at the bottom of the central column, if "SIDEBAR" it will be shown in the sidebar.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NORMAL', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'normal' => __('NORMAL', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                            'side' => __('SIDEBAR', 'custom-post-types'),
                            'advanced' => __('ADVANCED', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //order
                    'key' => 'order',
                    'label' => __('Order', 'custom-post-types'),
                    'info' => __('Field groups with a lower order will appear first', 'custom-post-types'),
                    'required' => false,
                    'type' => 'number',
                    'extra' => [
                        'placeholder' => __('ex: 10', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //supports
                    'key' => 'supports',
                    'label' => __('Assignment', 'custom-post-types'),
                    'info' => __('Choose for which CONTENT TYPE use this field group.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'multiple' => true,
                        'options' => Utils::getContentsOptions(),
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //admin only
                    'key' => 'admin_only',
                    'label' => __('Administrators only', 'custom-post-types'),
                    'info' => __('If set to "YES" only the administrators can create / modify these contents, if "NO" all the roles with the minimum capacity of "edit_posts".', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //show in rest
                    'key' => 'show_in_rest',
                    'label' => __('Show in rest', 'custom-post-types'),
                    'info' => __('If set to "YES" and the assigned content type is supported by REST API the meta values will be added to the response.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                $this->getRepeaterFields()
            ]
        ];
    }

    /**
     * @return array
     */
    public function getTaxonomyFields()
    {
        return [
            'id' => Utils::getInfo('ui_prefix') . '_tax',
            'label' => __('Taxonomy settings', 'custom-post-types'),
            'supports' => [[
                'type' => 'cpt',
                'id' => Utils::getInfo('ui_prefix') . '_tax'
            ]],
            'position' => 'normal',
            'order' => 0,
            'admin_only' => true,
            'fields' => [
                $this->uiRegistrationArgsTitleField,
                [ //singular
                    'key' => 'singular',
                    'label' => __('Singular', 'custom-post-types'),
                    'info' => __('Singular name.', 'custom-post-types'),
                    'required' => true,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //plural
                    'key' => 'plural',
                    'label' => __('Plural', 'custom-post-types'),
                    'info' => __('Plural name.', 'custom-post-types'),
                    'required' => true,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Partners', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //id
                    'key' => 'id',
                    'label' => __('ID', 'custom-post-types'),
                    'info' => __('Taxonomy ID.', 'custom-post-types'),
                    'required' => true,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //slug
                    'key' => 'slug',
                    'label' => __('Slug', 'custom-post-types'),
                    'info' => __('Permalink base for terms (if empty, plural is used).', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: partners', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'slug-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //supports
                    'key' => 'supports',
                    'label' => __('Assignment', 'custom-post-types'),
                    'info' => __('Choose for which POST TYPE use this taxonomy.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'multiple' => true,
                        'options' => Utils::getPostTypesOptions(),
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //public
                    'key' => 'public',
                    'label' => __('Public', 'custom-post-types'),
                    'info' => __('If set to "YES" it will be shown in the frontend and will have a permalink and a archive template.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('YES', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //admin only
                    'key' => 'admin_only',
                    'label' => __('Administrators only', 'custom-post-types'),
                    'info' => __('If set to "YES" only the administrators can create / modify these contents, if "NO" all the roles with the minimum capacity of "edit_posts".', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //hierarchical
                    'key' => 'hierarchical',
                    'label' => __('Hierarchical', 'custom-post-types'),
                    'info' => __('If set to "YES" it will be possible to set a parent TAXONOMY (as for the posts categories).', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                $this->uiRegistrationLabelsTitleField,
                [ //labels_add_new_item
                    'key' => 'labels_add_new_item',
                    'label' => __('Add new item', 'custom-post-types'),
                    'info' => __('The add new item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Add new partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_edit_item
                    'key' => 'labels_edit_item',
                    'label' => __('Edit item', 'custom-post-types'),
                    'info' => __('The edit item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Edit partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_new_item_name
                    'key' => 'labels_new_item_name',
                    'label' => __('New item name', 'custom-post-types'),
                    'info' => __('The new item name text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Partner name', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_view_item
                    'key' => 'labels_view_item',
                    'label' => __('View item', 'custom-post-types'),
                    'info' => __('The view item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: View partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_update_item
                    'key' => 'labels_update_item',
                    'label' => __('Update item', 'custom-post-types'),
                    'info' => __('The update item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Update partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_search_items
                    'key' => 'labels_search_items',
                    'label' => __('Search items', 'custom-post-types'),
                    'info' => __('The search item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Search partners', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_not_found
                    'key' => 'labels_not_found',
                    'label' => __('Not found', 'custom-post-types'),
                    'info' => __('The not found text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: No partner found', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_parent_item
                    'key' => 'labels_parent_item',
                    'label' => __('Parent item', 'custom-post-types'),
                    'info' => __('The parent item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Parent partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_parent_item_colon
                    'key' => 'labels_parent_item_colon',
                    'label' => __('Parent item', 'custom-post-types'),
                    'info' => __('The parent item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Parent partner', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_all_items
                    'key' => 'labels_all_items',
                    'label' => __('All items', 'custom-post-types'),
                    'info' => __('The all items text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: All partners', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                $this->uiRegistrationViewSwitchField
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPostTypeFields()
    {
        return [
            'id' => Utils::getInfo('ui_prefix'),
            'label' => __('Post type settings', 'custom-post-types'),
            'supports' => [[
                'type' => 'cpt',
                'id' => Utils::getInfo('ui_prefix')
            ]],
            'position' => 'normal',
            'order' => 0,
            'admin_only' => true,
            'fields' => [
                $this->uiRegistrationArgsTitleField,
                [ //singular
                    'key' => 'singular',
                    'label' => __('Singular', 'custom-post-types'),
                    'info' => __('Singular name.', 'custom-post-types'),
                    'required' => true,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //plural
                    'key' => 'plural',
                    'label' => __('Plural', 'custom-post-types'),
                    'info' => __('Plural name.', 'custom-post-types'),
                    'required' => true,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Products', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //id
                    'key' => 'id',
                    'label' => __('ID', 'custom-post-types'),
                    'info' => __('Post type ID.', 'custom-post-types'),
                    'required' => true,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: products', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //slug
                    'key' => 'slug',
                    'label' => __('Slug', 'custom-post-types'),
                    'info' => __('Permalink base for posts (if empty, plural is used).', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'slug-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //supports
                    'key' => 'supports',
                    'label' => __('Supports', 'custom-post-types'),
                    'info' => __('Set the available components when editing a post.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'multiple' => true,
                        'options' => [
                            'title' => __('Title', 'custom-post-types'),
                            'editor' => __('Editor', 'custom-post-types'),
                            'comments' => __('Comments', 'custom-post-types'),
                            'revisions' => __('Revisions', 'custom-post-types'),
                            'trackbacks' => __('Trackbacks', 'custom-post-types'),
                            'author' => __('Author', 'custom-post-types'),
                            'excerpt' => __('Excerpt', 'custom-post-types'),
                            'page-attributes' => __('Page attributes', 'custom-post-types'),
                            'thumbnail' => __('Thumbnail', 'custom-post-types'),
                            'custom-fields' => __('Custom fields', 'custom-post-types'),
                            'post-formats' => __('Post formats', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //menu_icon
                    'key' => 'menu_icon',
                    'label' => __('Menu icon', 'custom-post-types'),
                    'info' => __('Url to the icon, base64-encoded SVG using a data URI, name of a <a href="https://developer.wordpress.org/resource/dashicons" target="_blank" rel="nofolow">Dashicons</a> e.g. \'dashicons-chart-pie\'.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('dashicons-tag', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //public
                    'key' => 'public',
                    'label' => __('Public', 'custom-post-types'),
                    'info' => __('If set to "YES" it will be shown in the frontend and will have a permalink and a single template.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('YES', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //admin only
                    'key' => 'admin_only',
                    'label' => __('Administrators only', 'custom-post-types'),
                    'info' => __('If set to "YES" only the administrators can create / modify these contents, if "NO" all the roles with the minimum capacity of "edit_posts".', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //hierarchical
                    'key' => 'hierarchical',
                    'label' => __('Hierarchical', 'custom-post-types'),
                    'info' => __('If set to "YES" it will be possible to set a parent POST TYPE (as for pages).', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //has_archive
                    'key' => 'has_archive',
                    'label' => __('Has archive', 'custom-post-types'),
                    'info' => __('If set to "YES" the url of the post type archive will be reachable.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //exclude_from_search
                    'key' => 'exclude_from_search',
                    'label' => __('Exclude from search', 'custom-post-types'),
                    'info' => __('If set to "YES" these posts will be excluded from the search results.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //show_in_rest
                    'key' => 'show_in_rest',
                    'label' => __('Show in rest', 'custom-post-types'),
                    'info' => __('If set to "YES" API endpoints will be available (required for Gutenberg and other builders).', 'custom-post-types'),
                    'required' => false,
                    'type' => 'select',
                    'extra' => [
                        'placeholder' => __('YES', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                        'multiple' => false,
                        'options' => [
                            'true' => __('YES', 'custom-post-types') . ' - ' . __('Default', 'custom-post-types'),
                            'false' => __('NO', 'custom-post-types'),
                        ],
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                $this->uiRegistrationLabelsTitleField,
                [ //labels_add_new_item
                    'key' => 'labels_add_new_item',
                    'label' => __('Add new item', 'custom-post-types'),
                    'info' => __('The add new item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Add new product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_edit_item
                    'key' => 'labels_edit_item',
                    'label' => __('Edit item', 'custom-post-types'),
                    'info' => __('The edit item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Edit product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_new_item
                    'key' => 'labels_new_item',
                    'label' => __('New item', 'custom-post-types'),
                    'info' => __('The new item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: New product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_view_item
                    'key' => 'labels_view_item',
                    'label' => __('View item', 'custom-post-types'),
                    'info' => __('The view item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: View product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_view_items
                    'key' => 'labels_view_items',
                    'label' => __('View items', 'custom-post-types'),
                    'info' => __('The view items text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: View products', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_search_items
                    'key' => 'labels_search_items',
                    'label' => __('Search items', 'custom-post-types'),
                    'info' => __('The search item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Search products', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_not_found
                    'key' => 'labels_not_found',
                    'label' => __('Not found', 'custom-post-types'),
                    'info' => __('The not found text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: No product found', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_not_found_in_trash
                    'key' => 'labels_not_found_in_trash',
                    'label' => __('Not found in trash', 'custom-post-types'),
                    'info' => __('The not found in trash text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: No product found in trash', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_parent_item_colon
                    'key' => 'labels_parent_item_colon',
                    'label' => __('Parent item', 'custom-post-types'),
                    'info' => __('The parent item text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Parent product', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_all_items
                    'key' => 'labels_all_items',
                    'label' => __('All items', 'custom-post-types'),
                    'info' => __('The all items text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: All products', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                [ //labels_archives
                    'key' => 'labels_archives',
                    'label' => __('Archivies', 'custom-post-types'),
                    'info' => __('The archives text.', 'custom-post-types'),
                    'required' => false,
                    'type' => 'text',
                    'extra' => [
                        'placeholder' => __('ex: Product archives', 'custom-post-types')
                    ],
                    'wrap' => [
                        'width' => '',
                        'class' => 'advanced-field',
                        'id' => '',
                        'layout' => 'horizontal'
                    ]
                ],
                $this->uiRegistrationViewSwitchField
            ]
        ];
    }

    /**
     * @param $value
     * @param $key
     * @param $type
     * @param $content_type
     * @param $content_id
     * @return mixed
     */
    private function applyFieldGetFilters($value, $key, $type, $content_type, $content_id)
    {
        $output = $value;
        $type_get_callback = $this->getAvailableFieldGetCallback($type);
        if ($type_get_callback && !has_filter(Utils::getHookName("get_field_type_" . $type))) {
            add_filter(Utils::getHookName("get_field_type_" . $type), $type_get_callback);
        }
        $output = apply_filters(Utils::getHookName("get_field_type_" . $type), $output, $value, $content_type, $content_id);
        return apply_filters(Utils::getHookName("get_field_" . $key), $output, $value, $content_type, $content_id);
    }

    /**
     * @param $key
     * @param $post_id
     * @return string
     */
    public function getPostField($key, $post_id = false)
    {
        global $post;
        $post = $post_id && get_post($post_id) ? get_post($post_id) : $post;
        $core_fields = [
            'title' => get_the_title($post->ID),
            'content' => get_the_content($post->ID),
            'excerpt' => get_the_excerpt($post->ID),
            'thumbnail' => get_the_post_thumbnail($post->ID, 'full'),
            'author' => sprintf('<a href="%1$s" title="%2$s" aria-title="%2$s">%2$s</a>', get_author_posts_url(get_the_author_meta('ID')), get_the_author()),
            'written_date' => get_the_date(get_option('date_format', "d/m/Y"), $post->ID),
            'modified_date' => get_the_modified_date(get_option('date_format', "d/m/Y"), $post->ID),
        ];
        $value = isset($core_fields[$key]) ? $core_fields[$key] : get_post_meta($post->ID, $key, true);
        $post_type_fields = Utils::getFieldsByPostType($post->post_type);
        $type = isset($post_type_fields[$key]['type']) ? $post_type_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, $post->post_type, $post_id);
        return is_array($output) && !Utils::isRest() ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $key
     * @param $term_id
     * @return string
     */
    public function getTermField($key, $term_id = false)
    {
        $term = $term_id && get_term($term_id) ? get_term($term_id) : false;
        if (!$term) {
            return '';
        }
        $core_fields = [
            'name' => $term->name,
            'description' => $term->description
        ];
        $value = isset($core_fields[$key]) ? $core_fields[$key] : get_term_meta($term->term_id, $key, true);
        $taxonomy_fields = Utils::getFieldsByTaxonomy($term->taxonomy);
        $type = isset($taxonomy_fields[$key]['type']) ? $taxonomy_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, $term->taxonomy, $term_id);
        return is_array($output) && !Utils::isRest() ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $key
     * @param $option_id
     * @return string
     */
    public function getOptionField($key, $option_id = false)
    {
        $option = $option_id;
        if (!$option) {
            return '';
        }
        $value = get_option("$option-$key");
        $option_fields = Utils::getFieldsByOption($option);
        $type = isset($option_fields[$key]['type']) ? $option_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, 'option', $option);
        return is_array($output) ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $key
     * @param $user_id
     * @return string
     */
    public function getUserField($key, $user_id = false)
    {
        if (!$user_id) {
            return '';
        }
        $value = get_user_meta($user_id, $key, true);
        $users_fields = Utils::getFieldsByExtra('users');
        $type = isset($users_fields[$key]['type']) ? $users_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, 'user', $user_id);
        return is_array($output) && !Utils::isRest() ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $key
     * @param $attachment_id
     * @return string
     */
    public function getMediaField($key, $attachment_id = false)
    {
        if (!$attachment_id) {
            return '';
        }
        $value = get_post_meta($attachment_id, $key, true);
        $attachments_fields = Utils::getFieldsByExtra('attachment');
        $type = isset($attachments_fields[$key]['type']) ? $attachments_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, 'attachment', $attachment_id);
        return is_array($output) && !Utils::isRest() ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $key
     * @param $comment_id
     * @return string
     */
    public function geCommentField($key, $comment_id = false)
    {
        if (!$comment_id) {
            return '';
        }
        $value = get_comment_meta($comment_id, $key, true);
        $comments_fields = Utils::getFieldsByExtra('comment');
        $type = isset($comments_fields[$key]['type']) ? $comments_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, 'comment', $comment_id);
        return is_array($output) && !Utils::isRest() ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $key
     * @param $menu_item_id
     * @return string
     */
    public function geMenuItemField($key, $menu_item_id = false)
    {
        if (!$menu_item_id) {
            return '';
        }
        $value = get_post_meta($menu_item_id, $key, true);
        $comments_fields = Utils::getFieldsByExtra('menu-items');
        $type = isset($comments_fields[$key]['type']) ? $comments_fields[$key]['type'] : $key;
        $output = $this->applyFieldGetFilters($value, $key, $type, 'menu-item', $menu_item_id);
        return is_array($output) && !Utils::isRest() ? (current_user_can('edit_posts') ? '<pre>' . print_r($output, true) . '</pre>' : '') : $output;
    }

    /**
     * @param $route
     * @param $config
     * @param $getValueCallback
     * @return void
     */
    private function initRestFields($route, $config, $getValueCallback)
    {
        if (
            empty($route) ||
            empty($config) ||
            !is_array($config) ||
            empty($getValueCallback) ||
            !is_callable($getValueCallback)
        ) return;

        $groupId = $config['id'];
        if (!empty($config['show_in_rest'])) {
            $fields = !empty($config['fields']) && is_array($config['fields']) ? $config['fields'] : [];
            add_action('rest_api_init', function () use ($route, $groupId, $fields, $getValueCallback) {
                register_rest_field($route, $groupId, ['get_callback' => function ($item) use ($fields, $getValueCallback) {
                    $values = [];
                    foreach ($fields as $field) {
                        $values[$field['key']] = $getValueCallback($field['key'], $item['id']);
                    }
                    return $values;
                }]);
            });
        }
    }
}