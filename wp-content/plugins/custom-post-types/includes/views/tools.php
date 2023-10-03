<?php

use CustomPostTypes\includes\classes\Utils;

$mainPageUrl = admin_url('/edit.php?post_type=' . Utils::getInfo('ui_prefix') . '&page=tools');
$exportPageUrl = $mainPageUrl . '&action=export';
$importPageUrl = $mainPageUrl . '&action=import';
$requestPage = !empty($_GET['action']) && in_array($_GET['action'], ['export', 'import']) ? $_GET['action'] : 'main';
$isCurrent = function ($current) use ($requestPage) {
    return $current == $requestPage;
};
?>
<nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
    <?php
    printf(
        '<a href="%1$s" class="nav-tab %2$s" title="%3$s" aria-label="%3$s">%3$s</a>',
        $mainPageUrl,
        $isCurrent('main') ? 'nav-tab-active' : '',
        __('Infos', 'custom-post-types')
    );
    printf(
        '<a href="%1$s" class="nav-tab %2$s" title="%3$s" aria-label="%3$s">%3$s</a>',
        $exportPageUrl,
        $isCurrent('export') ? 'nav-tab-active' : '',
        __('Export', 'custom-post-types')
    );
    printf(
        '<a href="%1$s" class="nav-tab %2$s" title="%3$s" aria-label="%3$s">%3$s</a>',
        $importPageUrl,
        $isCurrent('import') ? 'nav-tab-active' : '',
        __('Import', 'custom-post-types')
    );
        printf(
            '<a href="%1$s" class="nav-tab" target="_blank" title="%2$s" aria-label="%2$s">%2$s <span class="dashicons dashicons-external" style="text-decoration: none;"></span></a>',
            Utils::getInfo('plugin_doc_url'),
            __('Documentation', 'custom-post-types')
        );
    ?>
</nav>
<div class="cpt-tools-page-content page-<?php echo $requestPage; ?>">
    <?php
    switch ($requestPage) {
        case 'import':
            echo '<p>' . __('This tool allows you to <u>import</u> all plugin settings (post types, taxonomies, field groups and templates).', 'custom-post-types') . '</p>';
            if (!has_action('custom-post-types-pro_import_page')) {
                echo Utils::getProBanner();
            } else {
                do_action('custom-post-types-pro_import_page');
            }
            break;
        case 'export':
            echo '<p>' . __('This tool allows you to <u>export</u> all plugin settings (post types, taxonomies, field groups and templates).', 'custom-post-types') . '</p>';
            if (!has_action('custom-post-types-pro_export_page')) {
                echo Utils::getProBanner();
            } else {
                do_action('custom-post-types-pro_export_page');
            }
            break;
        default:
            ?>
            <p>
                <?php _e('The purpose of the plugin is to <u>extend the features of the CMS</u> by adding custom content types without writing code or knowledge of development languages.', 'custom-post-types'); ?>
            </p>
            <p>
                <?php _e('This plugin is <strong>FREE</strong> and the developer guarantees frequent updates (for security and compatibility), if this plugin is useful <u>please support the development</u>.', 'custom-post-types'); ?>
            </p>
            <?php do_action('custom-post-types-pro_license_form'); ?>
            <div class="cpt-container">
                <div class="cpt-row">
                    <div class="cpt-col-3">
                        <h2><?php _e('Support the project', 'custom-post-types'); ?></h2>
                        <?php
                        if (!Utils::isProVersionActive()) {
                            printf(
                                '<p><a href="%1$s" class="button button-primary button-hero" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                                Utils::getInfo('plugin_url'),
                                __('Get PRO version', 'custom-post-types')
                            );
                        }
                        printf(
                            '<p><a href="%1$s" class="button button-primary" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            Utils::getInfo('plugin_donate_url'),
                            __('Make a Donation', 'custom-post-types')
                        );
                        printf(
                            '<p><a href="%1$s" class="button button-primary" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            Utils::getInfo('plugin_review_url'),
                            __('Write a Review', 'custom-post-types')
                        );
                        ?>
                    </div>
                    <div class="cpt-col-3">
                        <h2><?php _e('Other infos', 'custom-post-types'); ?></h2>
                        <?php
                        printf(
                            '<p><a href="%1$s" class="button button-secondary" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            Utils::getInfo('plugin_wporg_url'),
                            __('WordPress.org Plugin Page', 'custom-post-types')
                        );
                        printf(
                            '<p><a href="%1$s" class="button button-secondary" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            Utils::getInfo('plugin_support_url'),
                            __('Official Support Page', 'custom-post-types')
                        );
                        printf(
                            '<p><a href="%1$s" class="button button-secondary" target="_blank" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            Utils::getInfo('plugin_doc_url'),
                            __('Plugin Documentation', 'custom-post-types')
                        );
                        ?>
                    </div>
                    <div class="cpt-col-3">
                        <h2><?php _e('Tools', 'custom-post-types'); ?></h2>
                        <?php
                        printf(
                            '<p><a href="%1$s" class="button button-secondary" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            $exportPageUrl,
                            __('Export settings', 'custom-post-types')
                        );
                        printf(
                            '<p><a href="%1$s" class="button button-secondary" title="%2$s" aria-label="%2$s">%2$s</a></p>',
                            $importPageUrl,
                            __('Import settings', 'custom-post-types')
                        );
                        ?>
                    </div>
                </div>
            </div>
            <?php
            break;
    }
    ?>
</div>