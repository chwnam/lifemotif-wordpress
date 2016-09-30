<?php

namespace lmwp\modules;

use lmwp\controllers\AdminPageController;


class AdminPageModule implements StaticModuleInterface
{
    public static function init()
    {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'adminEnqueueScripts'));
        add_action('admin_menu', array(__CLASS__, 'addMenuPages'));
    }

    public static function adminEnqueueScripts()
    {
        wp_enqueue_style('admin-common', \lmwp\getAssetsUrl() . '/css/admin-common.css');
    }

    public static function addMenuPages()
    {
        \add_menu_page(
            __('LifeMotif', 'lmwp'),
            __('LifeMotif', 'lmwp'),
            'manage_options',
            'lm-admin-page',
            array(__CLASS__, 'outputAdminPages'),
            'dashicons-email-alt'
        );

        \add_submenu_page(
            'lm-admin-page',
            __('LifeMotif', 'lmwp'),
            __('LifeMotif', 'lmwp'),
            'manage_options',
            'lm-admin-page',
            array(__CLASS__, 'outputAdminPages')
        );

        \add_submenu_page(
            'lm-admin-page',
            __('LifeMotif Settings', 'lmwp'),
            __('Settings', 'lmwp'),
            'manage_options',
            'lm-settings-page',
            array(__CLASS__, 'outputSettingsPage')
        );
    }

    public static function outputAdminPages()
    {
        $controller = new AdminPageController();

        $controller->lifeMotifSummary();
    }

    public static function outputSettingsPage()
    {
        ?>
        <form method="post" action="<?php echo admin_url('options.php'); ?>">
            <?php
            \settings_fields('lm-options');
            \do_settings_sections('lm-settings-page');
            \submit_button();
            ?>
        </form>
        <?php
    }
}
