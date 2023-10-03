<?php
/*
Plugin Name: Custom post types
Plugin URI: https://totalpress.org/plugins/custom-post-types?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=custom-post-types
Description: Create / manage custom post types, custom taxonomies, custom admin pages, custom fields and custom templates easily, directly from the WordPress dashboard without writing code.
Author: TotalPress.org
Author URI: https://totalpress.org/?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=custom-post-types
Text Domain: custom-post-types
Domain Path: /languages/
Version: 4.0.12
*/

use CustomPostTypes\includes\classes\Core;
use CustomPostTypes\includes\classes\Utils;

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

define('CPT_INFOS', [
    'version' => get_file_data(__FILE__, ['Version' => 'Version'], false)['Version'],
    'path' => plugin_dir_path(__FILE__),
    'url' => plugin_dir_url(__FILE__),
    'plugin_url' => 'https://totalpress.org/plugins/custom-post-types?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=custom-post-types',
    'plugin_dev_url' => 'https://www.andreadegiovine.it/?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=custom-post-types',
    'plugin_doc_url' => 'https://totalpress.org/docs/custom-post-types.html?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=custom-post-types',
    'plugin_donate_url' => 'https://totalpress.org/donate?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=custom-post-types',
    'plugin_wporg_url' => 'https://wordpress.org/plugin/custom-post-types',
    'plugin_support_url' => 'https://wordpress.org/support/plugin/custom-post-types',
    'plugin_review_url' => 'https://wordpress.org/support/plugin/custom-post-types/reviews/#new-post',
    'hook_prefix' => 'cpt_',
    'ui_prefix' => 'manage_cpt',
    'options_prefix' => 'custom_post_types_',
    'nonce_key' => 'cpt-nonce'
], false);

// Autoload
foreach (
    array_merge(
        glob(CPT_INFOS['path'] . "includes/classes/*.php"),
        glob(CPT_INFOS['path'] . "includes/fields/*.php")
    ) as $filename
) {
    include_once $filename;
}

Core::getInstance();

do_action('custom_post_types_plugin_loaded');

$currentVersion = Utils::getInfo('version');
register_activation_hook(__FILE__, function () use ($currentVersion) {
    $request_url = add_query_arg(
        ['id' => 92, 'action' => 'activate', 'domain' => md5(get_home_url()), 'v' => $currentVersion],
        'https://totalpress.org/wp-json/totalpress/v1/plugin-growth'
    );
    wp_remote_get($request_url);
});
register_deactivation_hook(__FILE__, function () use ($currentVersion) {
    $request_url = add_query_arg(
        ['id' => 92, 'action' => 'deactivate', 'domain' => md5(get_home_url()), 'v' => $currentVersion],
        'https://totalpress.org/wp-json/totalpress/v1/plugin-growth'
    );
    wp_remote_get($request_url);
});