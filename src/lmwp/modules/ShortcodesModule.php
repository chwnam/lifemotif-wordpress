<?php
namespace lmwp\modules;

use lmwp\controllers\ShortcodesController;


class ShortcodesModule
{
    public static function init()
    {
        add_shortcode( 'lifemotif', array( __CLASS__, 'callbackLifeMotif' ) );
    }

    public static function callbackLifeMotif()
    {
        $controller = new ShortcodesController();

        return $controller->callForShortcode('displayLifeMotif');
    }
}