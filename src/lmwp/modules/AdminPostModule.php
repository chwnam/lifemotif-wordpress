<?php
namespace lmwp\modules;

use lmwp\controllers\AdminPostController;


class AdminPostModule implements StaticModuleInterface
{
    public static function init()
    {
        add_action('admin_post_update-archive', array(__CLASS__, 'updateArchive'));
        add_action('admin_post_update-mapping', array(__CLASS__, 'updateMapping'));
        add_action('admin_post_purge-mapping', array(__CLASS__, 'purgeMapping'));
    }

    public static function updateArchive()
    {
        $controller = new AdminPostController();
        $controller->updateArchive();
    }

    public static function updateMapping()
    {
        $controller = new AdminPostController();
        $controller->updateMapping();
    }

    public static function purgeMapping()
    {
        $controller = new AdminPostController();
        $controller->purgeMapping();
    }
}