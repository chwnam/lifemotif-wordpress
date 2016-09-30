<?php
namespace lmwp\modules;


class Sqlite3NotFoundModule implements StaticModuleInterface
{
    public static function init()
    {
        add_action( 'admin_notices', array( __CLASS__, 'outputSqlite3NotFound') );
    }

    public static function outputSqlite3NotFound() {
        echo '<div class="notice notice-error">';
        echo '<p>' . __( 'Cannot set up LifeMotif. SQLite3 PHP extension is not found!', 'lmwp' ) . '</p>';
        echo '</div>';
    }
}