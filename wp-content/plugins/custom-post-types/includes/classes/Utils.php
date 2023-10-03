<?php

namespace CustomPostTypes\includes\classes;

use WP_Rewrite;

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

class Utils
{
    /**
     * @param $name
     * @return mixed|null
     */
    public static function getInfo($name = '')
    {
        return defined('CPT_INFOS') && !empty(CPT_INFOS[$name]) ? CPT_INFOS[$name] : null;
    }

    /**
     * @param $name
     * @return string
     */
    public static function getHookName($name = '')
    {
        return self::getInfo('hook_prefix') . $name;
    }

    /**
     * @param $name
     * @return string
     */
    public static function getOptionName($name = '')
    {
        return self::getInfo('options_prefix') . $name;
    }

    /**
     * @param $postTypes
     * @return void
     */
    public static function flushRewriteRules($postTypes = [])
    {
        $ids = [];
        foreach ($postTypes as $postType) {
            if (!empty($postType['id'])) $ids[] = $postType['id'];
        }
        if (!empty($ids)) {
            $registeredIds = get_option(self::getOptionName('registered_cpt_ids'), []);
            $idsAlreadyRegistered = !array_diff($ids, $registeredIds);
            if (empty($registeredIds) || !$idsAlreadyRegistered) {
                $newRegisteredIds = array_merge($registeredIds, $ids);
                update_option(self::getOptionName('registered_cpt_ids'), array_unique($newRegisteredIds));
                flush_rewrite_rules();
            }
        }
    }

    /**
     * @return array
     */
    public static function getPostTypesOptions()
    {
        $registered_post_types = get_post_types(['_builtin' => false], 'objects');
        $exclude = self::getUiPostTypes();
        $post_types = [
            'post' => __('Posts'),
            'page' => __('Pages'),
        ];
        foreach ($registered_post_types as $post_type) {
            if (in_array($post_type->name, $exclude)) continue;
            $post_types[$post_type->name] = $post_type->label;
        }
        unset($registered_post_types);
        return $post_types;
    }

    /**
     * @return array
     */
    public static function getTaxonomiesOptions()
    {
        $registered_taxonomies = get_taxonomies(['_builtin' => false, 'show_ui' => true], 'objects');
        $taxonomies = [
            'category' => __('Categories'),
            'post_tag' => __('Tags'),
        ];
        foreach ($registered_taxonomies as $taxonomy) {
            $taxonomies[$taxonomy->name] = $taxonomy->label;
        }
        unset($registered_taxonomies);
        return $taxonomies;
    }

    /**
     * @return array[]
     */
    public static function getCoreSettingsPagesOptions()
    {
        return [
            'general' => ['title' => __('Settings') . ' > ' . _x('General', 'settings screen'), 'url' => 'options-general.php'],
            'writing' => ['title' => __('Settings') . ' > ' . __('Writing'), 'url' => 'options-writing.php'],
            'reading' => ['title' => __('Settings') . ' > ' . __('Reading'), 'url' => 'options-reading.php'],
            'discussion' => ['title' => __('Settings') . ' > ' . __('Discussion'), 'url' => 'options-discussion.php'],
            'media' => ['title' => __('Settings') . ' > ' . __('Media'), 'url' => 'options-media.php']
        ];
    }

    /**
     * @return array[]
     */
    public static function getSettingsPagesOptions()
    {
        $pages = self::getCoreSettingsPagesOptions();
        $registeredPages = self::getRegisteredPages();
        foreach ($registeredPages as $page) {
            $pages[$page['id']] = ['title' => $page['title']];
        }
        return $pages;
    }

    /**
     * @return array
     */
    public static function getRegisteredPages()
    {
        $registeredPages = get_posts([
            'posts_per_page' => -1,
            'post_type' => self::getInfo('ui_prefix') . '_page',
            'post_status' => 'publish'
        ]);

        $pagesByUi = [];

        foreach ($registeredPages as $page) {
            $pageId = !empty(get_post_meta($page->ID, 'id', true)) ? sanitize_title(get_post_meta($page->ID, 'id', true)) : sanitize_title($page->post_title);
            $pageParent = !empty(get_post_meta($page->ID, 'parent', true)) ? get_post_meta($page->ID, 'parent', true) : null;
            $pageOrder = is_numeric(get_post_meta($page->ID, 'order', true)) ? get_post_meta($page->ID, 'order', true) : null;
            $pageIcon = !empty(get_post_meta($page->ID, 'menu_icon', true)) ? get_post_meta($page->ID, 'menu_icon', true) : '';
            $pageAdminOnly = get_post_meta($page->ID, 'admin_only', true) == 'true';
            if ($pageParent && stripos($pageParent, '/') !== false) {
                $pageParent = explode('/', $pageParent);
                $pageParent = end($pageParent);
            }
            $pagesByUi[] = [
                'id' => $pageId,
                'parent' => $pageParent,
                'order' => $pageOrder,
                'menu_icon' => $pageIcon,
                'title' => $page->post_title,
                'content' => $page->post_content,
                'admin_only' => $pageAdminOnly
            ];
        }

        unset($registeredPages);

        return $pagesByUi;
    }

    /**
     * @return array
     */
    public static function getContentsOptions()
    {
        $options = [];

        $postTypes = self::getPostTypesOptions();
        foreach ($postTypes as $id => $label) {
            $options['-- ' . __('Post types', 'custom-post-types') . ' --']['cpt/' . $id] = $label;
        }
        unset($postTypes);

        $taxonomies = self::getTaxonomiesOptions();
        foreach ($taxonomies as $id => $label) {
            $options['-- ' . __('Taxonomies', 'custom-post-types') . ' --']['tax/' . $id] = $label;
        }
        unset($taxonomies);

        $settingsPages = self::getSettingsPagesOptions();
        foreach ($settingsPages as $id => $args) {
            $options['-- ' . __('Admin pages', 'custom-post-types') . ' --']['options/' . $id] = $args['title'];
        }
        unset($settingsPages);

        $options['-- ' . __('Extra', 'custom-post-types') . ' --'] = [
            'extra/users' => __('Users', 'custom-post-types'),
            'extra/media' => __('Media', 'custom-post-types'),
            'extra/comments' => __('Comments', 'custom-post-types'),
            'extra/menu-items' => __('Menu items', 'custom-post-types'),
        ];

        return $options;
    }

    /**
     * @return array
     */
    public static function getRolesOptions()
    {
        if (!function_exists('get_editable_roles')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        $registered_roles = get_editable_roles();
        $roles = [];
        foreach ($registered_roles as $role => $args) {
            $roles[$role] = $args['name'];
        }
        unset($registered_roles);
        return $roles;
    }

    /**
     * @param $post_id
     * @param $string
     * @return mixed|string
     */
    public static function getPostTitleWithParents($post_id = 0, $string = '')
    {
        $post = get_post($post_id);
        if ($post_id == 0 || !$post) return $string;
        $string = empty($string) ? $post->post_title : $string;
        if ($post->post_parent == 0) return $string;
        $string = get_the_title($post->post_parent) . ' > ' . $string;
        return self::getPostTitleWithParents($post->post_parent, $string);
    }

    /**
     * @param $term_id
     * @param $string
     * @return mixed|string
     */
    public static function getTermTitleWithParents($term_id = 0, $string = '')
    {
        $term = get_term($term_id);
        if ($term_id == 0 || !$term) return $string;
        $string = empty($string) ? $term->name : $string;
        if ($term->parent == 0) return $string;
        $string = get_term($term->parent)->name . ' > ' . $string;
        return self::getTermTitleWithParents($term->parent, $string);
    }

    /**
     * @return bool
     */
    public static function isRest()
    {
        $prefix = rest_get_url_prefix();
        if (
            defined('REST_REQUEST') && REST_REQUEST
            || isset($_GET['rest_route'])
            && strpos(trim($_GET['rest_route'], '\\/'), $prefix) === 0
        ) {
            return true;
        }
        global $wp_rewrite;
        if ($wp_rewrite === null) {
            $wp_rewrite = new WP_Rewrite();
        }
        $rest_url = wp_parse_url(trailingslashit(rest_url()));
        $current_url = wp_parse_url(add_query_arg(array()));
        return strpos($current_url['path'], $rest_url['path']) === 0;
    }

    /**
     * @param $post_type
     * @return array
     */
    public static function getFieldsByPostType($post_type = false)
    {
        if (!$post_type || !post_type_exists($post_type)) return [];
        $fields = [];
        if (post_type_supports($post_type, 'title')) $fields['title'] = ['label' => __('Post title', 'custom-post-types')];
        if (post_type_supports($post_type, 'editor')) $fields['content'] = ['label' => __('Post content', 'custom-post-types')];
        if (post_type_supports($post_type, 'excerpt')) $fields['excerpt'] = ['label' => __('Post excerpt', 'custom-post-types')];
        if (post_type_supports($post_type, 'thumbnail')) $fields['thumbnail'] = ['label' => __('Post image', 'custom-post-types')];
        if (post_type_supports($post_type, 'author')) $fields['author'] = ['label' => __('Post author', 'custom-post-types')];
        $fields['written_date'] = ['label' => __('Post date', 'custom-post-types')];
        $fields['modified_date'] = ['label' => __('Post modified date', 'custom-post-types')];
        $registered_fields = self::getFieldsBySupports("cpt/$post_type");
        return array_merge($fields, $registered_fields);
    }

    /**
     * @param $taxonomy
     * @return array
     */
    public static function getFieldsByTaxonomy($taxonomy = false)
    {
        if (!$taxonomy || !taxonomy_exists($taxonomy)) return [];
        $fields = [];
        $fields['name'] = ['label' => __('Term name', 'custom-post-types')];
        $fields['description'] = ['label' => __('Term description', 'custom-post-types')];
        $registered_fields = self::getFieldsBySupports("tax/$taxonomy");
        return array_merge($fields, $registered_fields);
    }

    /**
     * @param $option
     * @return array
     */
    public static function getFieldsByOption($option = false)
    {
        if (!$option) return [];
        return self::getFieldsBySupports("options/$option");
    }

    /**
     * @param $extra
     * @return array
     */
    public static function getFieldsByExtra($extra = false)
    {
        if (!$extra) return [];
        return self::getFieldsBySupports("extra/$extra");
    }

    /**
     * @param $support
     * @return array
     */
    public static function getFieldsBySupports($support)
    {
        $created_fields_groups = get_posts([
            'posts_per_page' => -1,
            'post_type' => self::getInfo('ui_prefix') . "_field",
            'meta_query' => [[
                'key' => 'supports',
                'value' => $support,
                'compare' => 'LIKE'
            ]]
        ]);
        $fields = [];
        foreach ($created_fields_groups as $created_fields_group) {
            $fields_group_fields = get_post_meta($created_fields_group->ID, 'fields', true);
            if (!empty($fields_group_fields)) {
                foreach ($fields_group_fields as $field) {
                    $fields[$field['key']] = [
                        'label' => $field['label'],
                        'type' => $field['type'],
                    ];
                }
            }
        }
        return $fields;
    }

    /**
     * @param $postType
     * @return array[]
     */
    public static function getTemplateShortcodes($postType)
    {
        $postTypeFields = self::getFieldsByPostType($postType);
        $postTypeTaxs = get_object_taxonomies($postType);

        $fieldShortcodes = [];
        foreach ($postTypeFields as $key => $field) {
            $fieldShortcodes[] = sprintf(
                '<input type="text" value="%1$s" title="%2$s" aria-label="%2$s" class="copy-shortcode" readonly>',
                htmlentities(sprintf('[cpt-field key="%s"]', $key)),
                __('Click to copy', 'custom-post-types')
            );
        }

        $taxShortcodes = [];
        foreach ($postTypeTaxs as $tax) {
            $taxShortcodes[] = sprintf(
                '<input type="text" value="%1$s" title="%2$s" aria-label="%2$s" class="copy-shortcode" readonly>',
                htmlentities(sprintf('[cpt-terms key="%s"]', $tax)),
                __('Click to copy', 'custom-post-types')
            );
        }

        return [
            'fields' => !empty($fieldShortcodes) ? $fieldShortcodes : ["<span>" . __("No shortcodes available", "custom-post-types") . "</span>"],
            'taxonomies' => !empty($taxShortcodes) ? $taxShortcodes : ["<span>" . __("No shortcodes available", "custom-post-types") . "</span>"]
        ];
    }

    /**
     * @return bool
     */
    public static function isProVersionActive()
    {
        return in_array('custom-post-types-pro/custom-post-types-pro.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    /**
     * @return string
     */
    public static function getProBanner()
    {
        $output = '<p><strong>' . __('This feature requires the <u>PRO version</u> and a valid license key.', 'custom-post-types') . '</strong></p>';
        if(!self::isProVersionActive()){
            $output .= sprintf(
                '<p><a href="%1$s" class="button button-primary button-hero" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                self::getInfo('plugin_url'),
                __('Get PRO version', 'custom-post-types')
            );
        }
        return '<div class="cpt-pro-banner">' . $output . '</div>';
    }

    /**
     * @return void
     */
    public static function registerProPages(){
        add_filter(self::getHookName('register_admin_pages'), function ($args) {
            $args[] = [
                'id' => 'manage_template',
                'parent' => 'edit.php?post_type=' . self::getInfo('ui_prefix'),
                'order' => null,
                'menu_icon' => null,
                'title' => __('Templates', 'custom-post-types'),
                'content' => self::getProBanner(),
                'admin_only' => true
            ];
            $args[] = [
                'id' => 'manage_admin_pages',
                'parent' => 'edit.php?post_type=' . self::getInfo('ui_prefix'),
                'order' => null,
                'menu_icon' => null,
                'title' => __('Admin pages', 'custom-post-types'),
                'content' => self::getProBanner(),
                'admin_only' => true
            ];
            $args[] = [
                'id' => 'manage_admin_notices',
                'parent' => 'edit.php?post_type=' . self::getInfo('ui_prefix'),
                'order' => null,
                'menu_icon' => null,
                'title' => __('Admin notices', 'custom-post-types'),
                'content' => self::getProBanner(),
                'admin_only' => true
            ];
            return $args;
        });
    }

    /**
     * @return void
     */
    public static function deregisterProPages(){
        add_filter(self::getHookName('register_admin_pages'), function ($args) {
            foreach ($args as $i => $page){
                if(in_array($page['id'], ['manage_template', 'manage_admin_pages', 'manage_admin_notices'])){
                    unset($args[$i]);
                }
            }
            return $args;
        });
    }

    /**
     * @return string
     */
    public static function getNoticesTitle()
    {
        return __('<strong>Custom post types</strong> notice:', 'custom-post-types');
    }

    /**
     * @param $args
     * @param $type
     * @return array
     */
    public static function getRegistrationErrorNoticeInfo($args = [], $type = 'post')
    {
        $idParts = [];
        foreach ($args as $arg) {
            $idParts[] = !empty($arg) ? (is_array($arg) ? count($arg) : $arg) : 'none';
        }
        return [
            'id' => $type . '_args_error_' . implode('_', $idParts),
            'details' => sprintf(
                '<pre class="error-code"><a href="#" title="%1$s" aria-label="%1$s">%1$s</a><code>%2$s</code></pre>',
                __('See registration args', 'custom-post-types'),
                json_encode($args, JSON_PRETTY_PRINT)
            )
        ];
    }

    /**
     * @return array
     */
    public static function getJsVariables()
    {
        return [
            'js_fields_events_hook' => 'cpt-fields-events',
            'js_fields_events_namespace' => 'custom-post-types',
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce(self::getInfo('nonce_key')),
        ];
    }

    /**
     * @return array
     */
    public static function getUiPostTypes(){
        return [
            self::getInfo('ui_prefix'),
            self::getInfo('ui_prefix') . "_tax",
            self::getInfo('ui_prefix') . "_field",
            self::getInfo('ui_prefix') . "_template",
            self::getInfo('ui_prefix') . "_page",
            self::getInfo('ui_prefix') . "_notice"
        ];
    }

    /**
     * @return string[]
     */
    public static function getPostTypeBlacklist(){
        $registered = array_keys(get_post_types());
        return $registered;
    }

    /**
     * @return string[]
     */
    public static function getTaxonomiesBlacklist(){
        $registered = array_keys(get_taxonomies());
        return $registered;
    }

    /**
     * @return string[]
     */
    public static function getAdminPagesBlacklist(){
        global $menu, $submenu;
        $registered = [
            'custom-post-types',
            'custom-post-types-pro',
        ];
        foreach ($menu as $registeredMenu) {
            if(
                empty($registeredMenu[2]) || // error
                strpos($registeredMenu[2], '.php') !== false || // core page
                (!empty($registeredMenu[4]) && $registeredMenu[4] == 'wp-menu-separator') // menu separator
            ){
                continue;
            }
            $registered[] = $registeredMenu[2];
        }
        foreach ($submenu as $registeredSubmenu) {
            foreach ($registeredSubmenu as $singleMenu) {
                if(
                    empty($singleMenu[2]) || // error
                    strpos($singleMenu[2], '.php') !== false // core page
                ){
                    continue;
                }
                $registered[] = $singleMenu[2];
            }
        }
        return $registered;
    }

    /**
     * @return array
     */
    public static function getUiPostTypesArgs() {
        $args = [];

        $default_args = [
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => false,
            'capabilities' => [
                'edit_post' => 'update_core',
                'read_post' => 'update_core',
                'delete_post' => 'update_core',
                'edit_posts' => 'update_core',
                'edit_others_posts' => 'update_core',
                'delete_posts' => 'update_core',
                'publish_posts' => 'update_core',
                'read_private_posts' => 'update_core'
            ],
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => [''],
            'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(Utils::getInfo('path') . 'assets/dashboard-icon.svg')),
            'can_export' => false,
        ];
        // Create/edit new post type
        $args[] = [
            'id' => self::getInfo('ui_prefix'),
            'singular' => __('Post type', 'custom-post-types'),
            'plural' => __('Post types', 'custom-post-types'),
            'labels' => [
                'name' => _x('Custom post types', 'Dashboard menu', 'custom-post-types'),
                'singular_name' => __('Post type', 'custom-post-types'),
                'menu_name' => __('Extend / Manage', 'custom-post-types'),
                'name_admin_bar' => __('Post type', 'custom-post-types'),
                'add_new' => __('Add post type', 'custom-post-types'),
                'add_new_item' => __('Add new post type', 'custom-post-types'),
                'new_item' => __('New post type', 'custom-post-types'),
                'edit_item' => __('Edit post type', 'custom-post-types'),
                'view_item' => __('View post type', 'custom-post-types'),
                'item_updated' => __('Post type updated', 'custom-post-types'),
                'all_items' => _x('Post types', 'Dashboard menu', 'custom-post-types'),
                'search_items' => __('Search post type', 'custom-post-types'),
                'not_found' => __('No post type available.', 'custom-post-types'),
                'not_found_in_trash' => __('No post type in the trash.', 'custom-post-types')
            ],
            'args' => array_replace_recursive($default_args, [
                'description' => __('Create and manage custom post types.', 'custom-post-types'),
            ]),
            'columns' => [
                'title' => [
                    'label' => __('Plural', 'custom-post-types'),
                ],
                'item_key' => [
                    'label' => __('ID', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        echo get_post_meta($post_id, 'id', true);
                    }
                ],
                'item_count' => [
                    'label' => __('Count', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        $key = get_post_meta($post_id, 'id', true);
                        if (empty($key) || !(isset(wp_count_posts($key)->publish) ? wp_count_posts($key)->publish : false)) {
                            echo "0";
                            return;
                        }
                        printf(
                            '<a href="%s" title="%s">%s</a>',
                            admin_url('edit.php?post_type=' . $key),
                            __('View', 'custom-post-types'),
                            wp_count_posts($key)->publish
                        );
                    }
                ],
                'date' => [],
            ]
        ];
        // Create/edit new tax
        $args[] = [
            'id' => self::getInfo('ui_prefix') . '_tax',
            'singular' => __('Taxonomy', 'custom-post-types'),
            'plural' => __('Taxonomies', 'custom-post-types'),
            'labels' => [
                'name' => __('Custom taxonomies', 'custom-post-types'),
                'singular_name' => __('Taxonomy', 'custom-post-types'),
                'menu_name' => __('Taxonomy', 'custom-post-types'),
                'name_admin_bar' => __('Taxonomy', 'custom-post-types'),
                'add_new' => __('Add taxonomy', 'custom-post-types'),
                'add_new_item' => __('Add new taxonomy', 'custom-post-types'),
                'new_item' => __('New taxonomy', 'custom-post-types'),
                'edit_item' => __('Edit taxonomy', 'custom-post-types'),
                'view_item' => __('View taxonomy', 'custom-post-types'),
                'item_updated' => __('Taxonomy updated', 'custom-post-types'),
                'all_items' => __('Taxonomies', 'custom-post-types'),
                'search_items' => __('Search taxonomy', 'custom-post-types'),
                'not_found' => __('No taxonomy available.', 'custom-post-types'),
                'not_found_in_trash' => __('No taxonomy in the trash.', 'custom-post-types')
            ],
            'args' => array_replace_recursive($default_args, [
                'description' => __('Create and manage custom taxonomies.', 'custom-post-types'),
                'show_in_menu' => 'edit.php?post_type=' . self::getInfo('ui_prefix')
            ]),
            'columns' => [
                'title' => [
                    'label' => __('Plural', 'custom-post-types'),
                ],
                'item_key' => [
                    'label' => __('ID', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        echo get_post_meta($post_id, 'id', true);
                    }
                ],
                'item_count' => [
                    'label' => __('Count', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        $key = get_post_meta($post_id, 'id', true);
                        if (empty($key) || is_wp_error(wp_count_terms(['taxonomy' => $key]))) {
                            echo "0";
                            return;
                        }
                        printf(
                            '<a href="%s" title="%s">%s</a>',
                            admin_url('edit-tags.php?taxonomy=' . $key),
                            __('View', 'custom-post-types'),
                            wp_count_terms(['taxonomy' => $key])
                        );
                    }
                ],
                'used_by' => [
                    'label' => __('Assignment', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        $supports = get_post_meta($post_id, 'supports', true);
                        if (empty($supports)) return;
                        $output = [];
                        foreach ($supports as $post_type) {
                            if (!get_post_type_object($post_type)) continue;
                            $output[] = sprintf(
                                '<a href="%s" title="%s">%s</a>',
                                admin_url('edit.php?post_type=' . $post_type),
                                __('View', 'custom-post-types'),
                                get_post_type_object($post_type)->labels->name
                            );
                        }
                        echo implode(', ', $output);
                    }
                ],
                'date' => [],
            ]
        ];
        // Create/edit new fieldsgroup
        $args[] = [
            'id' => self::getInfo('ui_prefix') . '_field',
            'singular' => __('Field group', 'custom-post-types'),
            'plural' => __('Field groups', 'custom-post-types'),
            'labels' => [
                'name' => __('Custom field groups', 'custom-post-types'),
                'singular_name' => __('Field group', 'custom-post-types'),
                'menu_name' => __('Field group', 'custom-post-types'),
                'name_admin_bar' => __('Field group', 'custom-post-types'),
                'add_new' => __('Add field group', 'custom-post-types'),
                'add_new_item' => __('Add new field group', 'custom-post-types'),
                'new_item' => __('New field group', 'custom-post-types'),
                'edit_item' => __('Edit field group', 'custom-post-types'),
                'view_item' => __('View field group', 'custom-post-types'),
                'item_updated' => __('Field group updated', 'custom-post-types'),
                'all_items' => __('Field groups', 'custom-post-types'),
                'search_items' => __('Search field group', 'custom-post-types'),
                'not_found' => __('No field group available.', 'custom-post-types'),
                'not_found_in_trash' => __('No field group in the trash.', 'custom-post-types')
            ],
            'args' => array_replace_recursive($default_args, [
                'description' => __('Create and manage custom field groups.', 'custom-post-types'),
                'show_in_menu' => 'edit.php?post_type=' . self::getInfo('ui_prefix'),
                'supports' => ['title']
            ]),
            'columns' => [
                'title' => [
                    'label' => __('Name', 'custom-post-types'),
                ],
                'item_count' => [
                    'label' => __('Fields', 'custom-post-types') . ' (' . __('Key', 'custom-post-types') . ')',
                    'callback' => function ($post_id) {
                        $fields = get_post_meta($post_id, 'fields', true);
                        if (empty($fields)) return;
                        $fields_labels_array = array_map(
                            function ($field) {
                                return $field['label'] . ' (' . $field['key'] . ')';
                            },
                            $fields
                        );
                        echo implode(', ', $fields_labels_array);
                    }
                ],
                'item_position' => [
                    'label' => __('Position', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        $available = [
                            '' => __('NORMAL', 'custom-post-types'),
                            'normal' => __('NORMAL', 'custom-post-types'),
                            'side' => __('SIDEBAR', 'custom-post-types'),
                            'advanced' => __('ADVANCED', 'custom-post-types'),
                        ];
                        echo $available[get_post_meta($post_id, 'position', true)];
                    }
                ],
                'used_by' => [
                    'label' => __('Assignment', 'custom-post-types'),
                    'callback' => function ($post_id) {
                        $supports = get_post_meta($post_id, 'supports', true);
                        if (empty($supports)) return;
                        $output = [];
                        foreach ($supports as $post_type) {
                            $content_type = 'cpt';
                            $content = $post_type;

                            if (strpos($post_type, '/') !== false) {
                                $content_type = explode('/', $post_type)[0];
                                $content = explode('/', $post_type)[1];
                            }

                            switch ($content_type) {
                                case 'cpt':
                                    if (get_post_type_object($content)) {
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            admin_url('edit.php?post_type=' . $content),
                                            __('View', 'custom-post-types'),
                                            get_post_type_object($content)->labels->name
                                        );
                                    }
                                    break;
                                case 'tax':
                                    if (get_taxonomy($content)) {
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            admin_url('edit-tags.php?taxonomy=' . $content),
                                            __('View', 'custom-post-types'),
                                            get_taxonomy($content)->labels->name
                                        );
                                    }
                                    break;
                                case 'extra':
                                    if ($content == 'users') {
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            admin_url('users.php'),
                                            __('View', 'custom-post-types'),
                                            __('Users')
                                        );
                                    }
                                    if ($content == 'media') {
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            admin_url('upload.php'),
                                            __('View', 'custom-post-types'),
                                            __('Media')
                                        );
                                    }
                                    if ($content == 'comments') {
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            admin_url('edit-comments.php'),
                                            __('View', 'custom-post-types'),
                                            __('Comments')
                                        );
                                    }
                                    if ($content == 'menu-items') {
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            admin_url('nav-menus.php'),
                                            __('View', 'custom-post-types'),
                                            __('Menu items')
                                        );
                                    }
                                    break;
                                case 'options':
                                    if (isset(self::getSettingsPagesOptions()[$content])) {
                                        $pageUrl = !empty(self::getSettingsPagesOptions()[$content]['url']) ? admin_url(self::getSettingsPagesOptions()[$content]['url']) : menu_page_url($content, false);
                                        $output[] = sprintf(
                                            '<a href="%s" title="%s">%s</a>',
                                            $pageUrl,
                                            __('View', 'custom-post-types'),
                                            self::getSettingsPagesOptions()[$content]['title']
                                        );
                                    }
                                    break;
                            }
                        }
                        echo implode(', ', $output);
                    }
                ],
                'date' => [],
            ]
        ];
        return $args;
    }

    /**
     * @return array
     */
    public static function getUiAdminPagesArgs() {
        $args = [];

        ob_start();
        require_once(self::getInfo('path') . 'includes/views/tools.php');
        $template = ob_get_clean();

        $args[] = [
            'id' => 'tools',
            'parent' => 'edit.php?post_type=' . self::getInfo('ui_prefix'),
            'order' => null,
            'menu_icon' => null,
            'title' => __('Tools & Infos', 'custom-post-types'),
            'content' => $template,
            'admin_only' => true
        ];
        return $args;
    }

    /**
     * @param $parent
     * @return bool
     */
    public static function currentUserCanAccessParentPage($parent){
        global $_wp_submenu_nopriv;
        if(isset($_wp_submenu_nopriv[ $parent ])){
            return false;
        }

        return true;
    }
}