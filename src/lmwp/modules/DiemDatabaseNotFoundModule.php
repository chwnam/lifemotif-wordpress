<?php
namespace lmwp\modules;


class DiemDatabaseNotFoundModule implements StaticModuleInterface
{
    public static function init()
    {
        add_action('admin_notices', array(__CLASS__, 'outputDiemDatabaseNotFound'));
    }

    public static function outputDiemDatabaseNotFound()
    {
        echo '<div class="notice notice-error">';
        echo '<p>' . __('Diem database path not found!', 'lmwp') . '</p>';
        echo '</div>';
    }
}
