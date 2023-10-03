<?php

namespace CustomPostTypes\includes\classes;

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

class Core
{
    /**
     * @var
     */
    private static $instance;

    /**
     * @var PostTypes
     */
    private $postTypes;

    /**
     * @var Taxonomies
     */
    private $taxonomies;

    /**
     * @var FieldGroups
     */
    private $fieldGroups;

    /**
     * @var AdminPages
     */
    private $adminPages;

    /**
     * @var AdminNotices
     */
    private $adminNotices;

    private function __construct()
    {
        // UI
        $this->registerUiPostTypes();
        $this->manipulateUiPostTypeTitle();
        $this->registerUiPages();
        $this->registerUiFields();
        $this->registerWelcomeNotices();
        $this->enqueueUiAssets();
        $this->initPluginUi();
        // Utilities
        $this->registerShortcodes();
        $this->ajaxAction();
        $this->applyUpdates();
        // Lets go
        $this->initRegisteredContents();
    }

    /**
     * @return Core
     */
    public static function getInstance()
    {
        // Check is $_instance has been set
        if(self::$instance instanceof Core)
        {
            // Returns the instance
            return self::$instance;
        }
        // Creates sets object to instance
        self::$instance = new Core();
    }

    /**
     * @return PostTypes
     */
    private function getPostTypes()
    {
        if ($this->postTypes instanceof PostTypes) {
            return $this->postTypes;
        }

        $this->postTypes = new PostTypes();
        return $this->postTypes;
    }

    /**
     * @return Taxonomies
     */
    private function getTaxonomies()
    {
        if ($this->taxonomies instanceof Taxonomies) {
            return $this->taxonomies;
        }

        $this->taxonomies = new Taxonomies();
        return $this->taxonomies;
    }

    /**
     * @return FieldGroups
     */
    private function getFieldGroups()
    {
        if ($this->fieldGroups instanceof FieldGroups) {
            return $this->fieldGroups;
        }

        $this->fieldGroups = new FieldGroups();
        return $this->fieldGroups;
    }

    /**
     * @return AdminPages
     */
    private function getAdminPages()
    {
        if ($this->adminPages instanceof AdminPages) {
            return $this->adminPages;
        }

        $this->adminPages = new AdminPages();
        return $this->adminPages;
    }

    /**
     * @return AdminNotices
     */
    public function getAdminNotices()
    {
        if ($this->adminNotices instanceof AdminNotices) {
            return $this->adminNotices;
        }

        $this->adminNotices = new AdminNotices();
        return $this->adminNotices;
    }

    /**
     * @return void
     *
     * @see \CustomPostTypes\includes\classes\Utils::getUiPostTypesArgs
     */
    private function registerUiPostTypes()
    {
        // Remove quick edit links
        add_filter('post_row_actions', function ($actions, $post) {
            $postType = $post->post_type;
            if (stripos($postType, Utils::getInfo('ui_prefix')) !== false) {
                unset($actions['inline hide-if-no-js']);
            }
            return $actions;
        }, 1, 2);

        // Update ui notices
        add_filter('post_updated_messages', function ($messages) {
            $messages[Utils::getInfo('ui_prefix')] = [
                1 => __('Post type updated', 'custom-post-types'),
                4 => __('Post type updated', 'custom-post-types'),
                6 => __('Post type published', 'custom-post-types'),
                7 => __('Post type saved', 'custom-post-types'),
                8 => __('Post type submitted', 'custom-post-types'),
                9 => __('Post type scheduled', 'custom-post-types'),
                10 => __('Post type draft updated', 'custom-post-types'),
            ];
            $messages[Utils::getInfo('ui_prefix') . '_tax'] = [
                1 => __('Taxonomy updated', 'custom-post-types'),
                4 => __('Taxonomy updated', 'custom-post-types'),
                6 => __('Taxonomy published', 'custom-post-types'),
                7 => __('Taxonomy saved', 'custom-post-types'),
                8 => __('Taxonomy submitted', 'custom-post-types'),
                9 => __('Taxonomy scheduled', 'custom-post-types'),
                10 => __('Taxonomy draft updated', 'custom-post-types'),
            ];
            $messages[Utils::getInfo('ui_prefix') . '_field'] = [
                1 => __('Field group updated', 'custom-post-types'),
                4 => __('Field group updated', 'custom-post-types'),
                6 => __('Field group published', 'custom-post-types'),
                7 => __('Field group saved', 'custom-post-types'),
                8 => __('Field group submitted', 'custom-post-types'),
                9 => __('Field group scheduled', 'custom-post-types'),
                10 => __('Field group draft updated', 'custom-post-types'),
            ];
            $messages[Utils::getInfo('ui_prefix') . '_template'] = [
                1 => __('Template updated', 'custom-post-types'),
                4 => __('Template updated', 'custom-post-types'),
                6 => __('Template published', 'custom-post-types'),
                7 => __('Template saved', 'custom-post-types'),
                8 => __('Template submitted', 'custom-post-types'),
                9 => __('Template scheduled', 'custom-post-types'),
                10 => __('Template draft updated', 'custom-post-types'),
            ];
            $messages[Utils::getInfo('ui_prefix') . '_page'] = [
                1 => __('Admin page updated', 'custom-post-types'),
                4 => __('Admin page updated', 'custom-post-types'),
                6 => __('Admin page published', 'custom-post-types'),
                7 => __('Admin page saved', 'custom-post-types'),
                8 => __('Admin page submitted', 'custom-post-types'),
                9 => __('Admin page scheduled', 'custom-post-types'),
                10 => __('Admin page draft updated', 'custom-post-types'),
            ];
            return $messages;
        });
    }

    /**
     * @return void
     */
    private function manipulateUiPostTypeTitle()
    {
        $no_title_ui_cpts = [Utils::getInfo('ui_prefix'), Utils::getInfo('ui_prefix') . '_tax'];

        // Override ui post title using singular label
        add_action('save_post', function ($post_id) use ($no_title_ui_cpts) {
            $post_type = get_post($post_id)->post_type;
            $post_status = get_post($post_id)->post_status;
            if (!in_array($post_type, $no_title_ui_cpts) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || $post_status == 'trash') return $post_id;
            $new_title = !empty($_POST['meta-fields']['plural']) ? $_POST['meta-fields']['plural'] : 'CPT_' . $post_id;
            global $wpdb;
            $wpdb->update($wpdb->posts, ['post_title' => $new_title], ['ID' => $post_id]);
            return $post_id;
        });

        // Show on ui post types title
        add_action('edit_form_after_title', function () use ($no_title_ui_cpts) {
            $screen = get_current_screen();
            $post = isset($_GET['post']) && get_post($_GET['post']) ? get_post($_GET['post']) : false;
            if (!in_array($screen->post_type, $no_title_ui_cpts) || !in_array($screen->id, $no_title_ui_cpts) || !$post) return;
            printf('<h1 style="padding: 0;">%s</h1>', $post->post_title);
        });
    }

    /**
     * @return void
     *
     * @see \CustomPostTypes\includes\classes\Utils::getUiAdminPagesArgs
     */
    private function registerUiPages()
    {
        // Remove new post type menu
        add_action('admin_menu', function () {
            remove_submenu_page('edit.php?post_type=' . Utils::getInfo('ui_prefix'), 'post-new.php?post_type=' . Utils::getInfo('ui_prefix'));
        });

        Utils::registerProPages();
    }

    /**
     * @return void
     */
    private function registerUiFields()
    {
        // Register ui fields
        add_filter(Utils::getHookName('register_fields'), function ($fields) {
            $fields[] = $this->getFieldGroups()->getPostTypeFields();
            $fields[] = $this->getFieldGroups()->getTaxonomyFields();
            $fields[] = $this->getFieldGroups()->getNewFieldGroupFields();
            return $fields;
        });
    }

    /**
     * @return void
     */
    private function registerWelcomeNotices()
    {
        // Register welcome notices
        add_filter(Utils::getHookName('register_notices'), function ($args) {
            $buttons = [
                [
                    'link' => Utils::getInfo('plugin_review_url'),
                    'label' => __('Write a Review', 'custom-post-types'),
                    'target' => '_blank',
                    'cta' => true
                ],
                [
                    'link' => Utils::getInfo('plugin_donate_url'),
                    'label' => __('Make a Donation', 'custom-post-types'),
                    'target' => '_blank'
                ]
            ];
            if (!Utils::isProVersionActive()) {
                $buttons[] = [
                    'link' => Utils::getInfo('plugin_url'),
                    'label' => __('Get PRO version', 'custom-post-types'),
                    'target' => '_blank'
                ];
            }

            // After installation notice
            $welcomeNotice = [
                'id' => 'welcome_notice_400',
                'title' => Utils::getNoticesTitle(),
                'message' => __('Thanks for using this plugin! Do you want to help us grow to add new features?', 'custom-post-types') . '<br><br>' . sprintf(__('The new version %s introduces a lot of new features and improves the core of the plugin.<br>For any problems you can download the previous version %s from the official page of the plugin from WordPress.org (Advanced View > Previous version).', 'custom-post-types'), '<u>' . Utils::getInfo('version') . '</u>', '<u>3.1.1</u>'),
                'type' => 'success',
                'dismissible' => true,
                'admin_only' => 'true',
                'buttons' => $buttons,
            ];

            if (time() < 1688169599) { // 30-06-2023 23:59:59
                $welcomeNotice['message'] = $welcomeNotice['message'] . '<br><br>' . sprintf('Use the coupon <strong><u>%s</u></strong> and get the PRO version with special discount until %s.', 'WELCOME-CPT-4', '30/06/2023');
            }

            $args[] = $welcomeNotice;

            $installationTime = get_option(Utils::getOptionName('installation_time'), null);

            if ($installationTime && strtotime("+7 day", $installationTime) < time()) {
                // After 7 days notice
                $args[] = [
                    'id' => 'welcome_notice_400_1',
                    'title' => Utils::getNoticesTitle(),
                    'message' => __('Wow! More than 7 days of using this amazing plugin. Your support is really important.', 'custom-post-types'),
                    'type' => 'success',
                    'dismissible' => true,
                    'admin_only' => 'true',
                    'buttons' => $buttons,
                ];
            }

            if ($installationTime && strtotime("+30 day", $installationTime) < time()) {
                // After 30 days notice
                $args[] = [
                    'id' => 'welcome_notice_400_1',
                    'title' => Utils::getNoticesTitle(),
                    'message' => __('Wow! More than 30 days of using this amazing plugin. Your support is really important.', 'custom-post-types'),
                    'type' => 'success',
                    'dismissible' => true,
                    'admin_only' => 'true',
                    'buttons' => $buttons,
                ];
            }

            if (
                !Utils::isProVersionActive() &&
                $installationTime && strtotime("+3 day", $installationTime) < time()
            ) {
                $buttons2 = array_reverse($buttons);
                unset($buttons2[2]);
                $buttons2[0]['cta'] = true;
                // PRO
                $args[] = [
                    'id' => 'welcome_notice_pro',
                    'title' => Utils::getNoticesTitle(),
                    'message' => '<p style="font-size: 1.3em;">' . __("It's time to PRO, <u>go to the next level</u>:", 'custom-post-types') . '</p><p style="font-size: 1.3em; font-weight: bold;">⚡ Custom templates<br>⚡ Custom admin pages<br>⚡ Custom admin notices<br>⚡ +6 fields types<br>⚡ Export/Import settings</p><p style="font-size: 1.3em;">' . __("now you are ready, one small step, one big change!", 'custom-post-types') . '</p>',
                    'type' => 'success',
                    'dismissible' => true,
                    'admin_only' => 'true',
                    'buttons' => $buttons2,
                ];
            }

            return $args;
        });
    }

    /**
     * @return void
     */
    private function enqueueUiAssets()
    {
        // Enqueue ui assets
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style(Utils::getInfo('ui_prefix'), Utils::getInfo('url') . 'assets/css/backend.css');
            if ($this->loadJs()) {
                wp_enqueue_media();
                wp_enqueue_editor();
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script(Utils::getInfo('options_prefix'), Utils::getInfo('url') . 'assets/js/backend.js', ['jquery', 'wp-i18n', 'wp-util', 'wp-hooks', 'wp-editor', 'wp-color-picker'], null, true);
                wp_localize_script(Utils::getInfo('options_prefix'), 'cpt', Utils::getJsVariables());
                wp_set_script_translations(Utils::getInfo('options_prefix'), 'custom-post-types');
            }
        });
    }

    /**
     * @return void
     */
    private function initPluginUi()
    {
        add_filter('plugin_action_links', function ($links, $file) {
            if ($file == 'custom-post-types/custom-post-types.php') {
                $links[] = sprintf(
                    '<a href="%1$s" target="_blank" aria-label="%2$s"> %2$s </a>',
                    Utils::getInfo('plugin_support_url'),
                    __('Support', 'custom-post-types')
                );
                if (!Utils::isProVersionActive()) {
                    $links[] = sprintf(
                        '<a href="%1$s" target="_blank" aria-label="%2$s" style="font-weight: bold;"> %2$s </a>',
                        Utils::getInfo('plugin_url'),
                        __('Get PRO', 'custom-post-types')
                    );
                }
            }
            return $links;
        }, PHP_INT_MAX, 2);
    }

    /**
     * @return void
     */
    private function registerShortcodes()
    {
        // Shortcodes
        add_action('wp', function () {
            if (!is_admin() && !Utils::isRest()) {
                global $post;
                add_shortcode('cpt-field', function ($atts) {
                    $a = shortcode_atts([
                        'key' => false,
                        'post-id' => false
                    ], $atts);
                    $errors = false;
                    if (!$a['key']) {
                        $errors[] = __('Missing field "key".', 'custom-post-types');
                    }
                    if ($errors) {
                        return current_user_can('edit_posts') ? "<pre>" . implode("</pre><pre>", $errors) . "</pre>" : '';
                    }
                    return $this->getFieldGroups()->getPostField($a['key'], $a['post-id']);
                });
                add_shortcode('cpt-terms', function ($atts) use ($post) {
                    $a = shortcode_atts([
                        'key' => false,
                        'post-id' => false
                    ], $atts);
                    $errors = false;
                    if (!$a['key']) {
                        $errors[] = __('Missing field "key".', 'custom-post-types');
                    }
                    if ($errors) {
                        return current_user_can('edit_posts') ? "<pre>" . implode("</pre><pre>", $errors) . "</pre>" : '';
                    }
                    $post = $a['post-id'] && get_post($a['post-id']) ? get_post($a['post-id']) : $post;
                    $get_terms = get_the_terms($post->ID, $a['key']);
                    $terms = [];
                    foreach ($get_terms as $term) {
                        $terms[] = sprintf('<a href="%1$s" title="%2$s" aria-title="%2$s">%2$s</a>', get_term_link($term->term_id), $term->name);
                    }
                    return implode(', ', $terms);
                });
                add_shortcode('cpt-term-field', function ($atts) {
                    $a = shortcode_atts([
                        'key' => false,
                        'term-id' => false
                    ], $atts);
                    $errors = false;
                    if (!$a['key']) {
                        $errors[] = __('Missing field "key".', 'custom-post-types');
                    }
                    if (!$a['term-id']) {
                        $errors[] = __('Missing field "term-id".', 'custom-post-types');
                    }
                    if ($errors) {
                        return current_user_can('edit_posts') ? "<pre>" . implode("</pre><pre>", $errors) . "</pre>" : '';
                    }
                    return $this->getFieldGroups()->getTermField($a['key'], $a['term-id']);
                });
                add_shortcode('cpt-option-field', function ($atts) {
                    $a = shortcode_atts([
                        'key' => false,
                        'option-id' => false
                    ], $atts);
                    $errors = false;
                    if (!$a['key']) {
                        $errors[] = __('Missing field "key".', 'custom-post-types');
                    }
                    if (!$a['option-id']) {
                        $errors[] = __('Missing field "option-id".', 'custom-post-types');
                    }
                    if ($errors) {
                        return current_user_can('edit_posts') ? "<pre>" . implode("</pre><pre>", $errors) . "</pre>" : '';
                    }
                    return $this->getFieldGroups()->getOptionField($a['key'], $a['option-id']);
                });
            }
        });
    }

    /**
     * @return void
     */
    private function ajaxAction()
    {
        $this->getAdminNotices()->ajaxAction();

        add_action('init', function(){
            $ajaxActions = (array)apply_filters(Utils::getHookName('register_ajax_actions'), []);
            foreach ($ajaxActions as $action => $args) {
                add_action('wp_ajax_' . $action, function () use ($args) {
                    $nonce = !empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], Utils::getInfo('nonce_key'));
                    if (!$nonce) {
                        wp_send_json_error();
                    }
                    foreach ($args['requiredParams'] as $param) {
                        if (empty($_REQUEST[$param])) {
                            wp_send_json_error();
                        }
                    }
                    if (empty($args['callback']) || !is_callable($args['callback'])) {
                        wp_send_json_error();
                    }
                    $result = $args['callback']($_REQUEST);
                    wp_send_json_success($result);
                });
            }
        });
    }

    /**
     * @return void
     */
    private function applyUpdates()
    {
        $installedVersion = get_option(Utils::getOptionName('version'), null);
        $currentVersion = Utils::getInfo('version');

        if (version_compare($installedVersion, $currentVersion, '=')) {
            return;
        }

        if (version_compare($installedVersion, $currentVersion, '<')) {
            // Apply updates
        }

        update_option(Utils::getOptionName('version'), $currentVersion);
        update_option(Utils::getOptionName('installation_time'), time());

        if(!empty($installedVersion)){
            $request_url = add_query_arg(
                ['id' => 92, 'action' => 'updated', 'domain' => md5(get_home_url()), 'v' => $currentVersion],
                'https://totalpress.org/wp-json/totalpress/v1/plugin-growth'
            );
            wp_remote_get($request_url);
        }
    }

    /**
     * @return void
     */
    private function initRegisteredContents()
    {
        // Init registered content
        add_action('init', function () {
            $this->getPostTypes()->initRegisteredPostTypes();
            $this->getTaxonomies()->initRegisteredTaxonomies();
            $this->getFieldGroups()->initRegisteredGroups();
            $this->getAdminPages()->initRegisteredPages();
        });
        add_action('admin_init', function () {
            $this->getAdminNotices()->initRegisteredNotices();
        });
    }

    /**
     * @return bool
     */
    public function loadJs()
    {
        $currentScreen = get_current_screen();
        if (
            (!empty($currentScreen->id) &&
                (
                    in_array($currentScreen->id, $this->getFieldGroups()->screensWithFields) ||
                    (
                        explode('_page_', $currentScreen->id) &&
                        !empty(explode('_page_', $currentScreen->id)[1]) &&
                        in_array('_page_' . explode('_page_', $currentScreen->id)[1], $this->getFieldGroups()->screensWithFields)
                    )
                )
            ) ||
            $this->getAdminNotices()->hasNotices
        ) {
            return true;
        }
        return false;
    }
}