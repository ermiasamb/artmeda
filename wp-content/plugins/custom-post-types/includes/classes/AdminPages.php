<?php

namespace CustomPostTypes\includes\classes;

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

class AdminPages
{

    /**
     * @return void
     */
    public function initRegisteredPages()
    {
        add_action('admin_menu', function () {
            $pagesByUi = Utils::getRegisteredPages();

            $pages = (array)apply_filters(Utils::getHookName('register_admin_pages'), $pagesByUi);

            $pagesByCore = Utils::getUiAdminPagesArgs();

            $pages = array_merge($pagesByCore, $pages);

            if (empty($pages)) return;

            foreach ($pages as $i => $page) {
                $id = !empty($page['id']) && is_string($page['id']) ? $page['id'] : false;
                $parent = !empty($page['parent']) && is_string($page['parent']) ? $page['parent'] : false;
                $order = !empty($page['order']) && is_numeric($page['order']) ? $page['order'] : null;
                $icon = !empty($page['menu_icon']) && is_string($page['menu_icon']) ? $page['menu_icon'] : '';
                $title = !empty($page['title']) && is_string($page['title']) ? $page['title'] : false;
                $content = !empty($page['content']) && is_string($page['content']) ? $page['content'] : false;
                $capability = !empty($page['admin_only']) ? 'administrator' : 'edit_posts';

                if($parent && !Utils::currentUserCanAccessParentPage($parent)){
                    return;
                }

                $errorInfo = Utils::getRegistrationErrorNoticeInfo($page, 'page');

                if (!$id || !$title) {
                    add_filter(Utils::getHookName('register_notices'), function ($args) use ($errorInfo) {
                        $args[] = [
                            'id' => $errorInfo['id'],
                            'title' => Utils::getNoticesTitle(),
                            'message' => __('Admin page registration was not successful ("id" and "title" args are required).', 'custom-post-types') . $errorInfo['details'],
                            'type' => 'error',
                            'dismissible' => 3,
                            'admin_only' => 'true',
                            'buttons' => false,
                        ];
                        return $args;
                    });
                    unset($pages[$i]);
                    continue;
                }

                if(in_array($id, Utils::getAdminPagesBlacklist())){
                    add_filter(Utils::getHookName('register_notices'), function ($args) use ($errorInfo) {
                        $args[] = [
                            'id' => $errorInfo['id'],
                            'title' => Utils::getNoticesTitle(),
                            'message' => __('Admin page reserved or already registered, try a different "id".', 'custom-post-types') . $errorInfo['details'],
                            'type' => 'error',
                            'dismissible' => 3,
                            'admin_only' => 'true',
                            'buttons' => false,
                        ];
                        return $args;
                    });
                    unset($pages[$i]);
                    continue;
                }

                $callback = function () use ($id, $title, $content) {
                    $this->pageCallback($id, $title, $content);
                };

                if ($parent) {
                    $registeredAdminPage = add_submenu_page($parent, $title, $title, $capability, $id, $callback, $order);
                } else {
                    $registeredAdminPage = add_menu_page($title, $title, $capability, $id, $callback, $icon, $order);
                }

                if (!$registeredAdminPage) {
                    add_filter(Utils::getHookName('register_notices'), function ($args) use ($errorInfo) {
                        $args[] = [
                            'id' => $errorInfo['id'] . '_core',
                            'title' => Utils::getNoticesTitle(),
                            'message' => __('Admin page registration was not successful.', 'custom-post-types') . $errorInfo['details'],
                            'type' => 'error',
                            'dismissible' => 3,
                            'admin_only' => 'true',
                            'buttons' => false,
                        ];
                        return $args;
                    });
                    unset($pages[$i]);
                }
            }

            unset($pages);
        });
    }

    /**
     * @param $id
     * @param $title
     * @param $content
     * @return void
     */
    public function pageCallback($id, $title, $content = false)
    { ?>
        <div class="wrap cpt-admin-page">
            <h1 class="cpt-admin-page-title"><?php echo $title; ?></h1>
            <?php
            if (!empty($content)) {
                printf('<div class="cpt-admin-page-content">%s</div>', $id == 'tools' ? $content : apply_filters('the_content', $content));
            }
            ob_start();
            do_settings_sections($id);
            $fields = ob_get_clean();
            if(!empty($fields)) { ?>
                <form method="post" action="options.php" novalidate="novalidate">
                    <?php settings_fields($id);?>
                    <?php echo $fields;?>
                    <?php submit_button();?>
                </form>
            <?php }?>
        </div>
    <?php }
}