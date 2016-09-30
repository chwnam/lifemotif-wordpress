<?php
namespace lmwp\modules;

use lmwp\controllers\AdminAjaxController;


class AdminAjaxModule implements StaticModuleInterface
{
    public static function init()
    {
        add_action('wp_ajax_get-article-catalog', array(__CLASS__, 'getArticleCatalog'));
        add_action('wp_ajax_get-article', array(__CLASS__, 'getArticle'));
    }

    public static function getArticleCatalog()
    {
        $controller = new AdminAjaxController();
        $controller->getArticleCatalog();
    }

    public static function getArticle()
    {
        $controller = new AdminAjaxController();
        $controller->getArticle();
    }
}