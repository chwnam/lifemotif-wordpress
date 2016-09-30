<?php

namespace lmwp\modules;


class AdminSettingsPageModule implements StaticModuleInterface
{
    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'initSettings'));

        add_action('admin_init', array(__CLASS__, 'initSections'));

        add_action('admin_init', array(__CLASS__, 'initFields'));
    }

    public static function initSettings()
    {
        register_setting('lm-options', 'lm-profile-path', '');
        register_setting('lm-options', 'lm-log-level', '');
        register_setting('lm-options', 'lm-log-file', '');
        register_setting('lm-options', 'lm-diem-path', '');
        register_setting('lm-options', 'lm-python-path', '');
    }

    public static function initSections()
    {
        add_settings_section('lm-settings-section', __('LifeMotif Settings', 'lmwp'), '', 'lm-settings-page');
    }

    public static function initFields()
    {
        add_settings_field(
            'profile-path',
            __('Profile Path', 'lmwp'),
            array(__CLASS__, 'outputProfilePath'),
            'lm-settings-page',
            'lm-settings-section'
        );

        add_settings_field(
            'log-level',
            __('Log Level', 'lmwp'),
            array(__CLASS__, 'outputLogLevel'),
            'lm-settings-page',
            'lm-settings-section'
        );

        add_settings_field(
            'log-file', __('Log File', 'lmwp'),
            array(__CLASS__, 'outputLogFile'),
            'lm-settings-page',
            'lm-settings-section'
        );

        add_settings_field(
            'diem-path',
            __('Diem Path', 'lmwp'),
            array(__CLASS__, 'outputDiemPath'),
            'lm-settings-page',
            'lm-settings-section'
        );

        add_settings_field(
            'python-path',
            __('Python Path', 'lmwp'),
            array(__CLASS__, 'outputPythonPath'),
            'lm-settings-page',
            'lm-settings-section'
        );
    }

    public static function outputProfilePath()
    {
        $id_name = 'lm-profile-path';
        $val     = esc_attr( get_option( $id_name ) );

        echo '<input type="text" class="input long-input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
        echo '<span class="description">' . __( 'Diem\'s profile path', 'lmwp' ) . '</span>';
    }

    public static function outputLogLevel()
    {
        $id_name = 'lm-log-level';
        $val     = esc_attr( get_option( $id_name ) );

        $levels = array( 'CRITICAL', 'ERROR', 'WARNING', 'INFO', 'DEBUG' );

        echo "<select id=\"$id_name\" name=\"$id_name\">";
        foreach( $levels as $level ) {
            echo "<option value=\"$level\" " . selected( $val, $level, FALSE ) . ">$level</option>";
        }
        echo "</select>";

        echo '<span class="description">' . __( 'Diem\'s log level', 'lmwp' ) . '</span>';
    }

    public static function outputLogFile()
    {
        $id_name = 'lm-log-file';
        $val     = esc_attr( get_option( $id_name ) );

        echo '<input type="text" class="input long-input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
        echo '<span class="description">' . __( 'Diem\'s log file path', 'lmwp' ) . '</span>';
    }

    public static function outputDiemPath()
    {
        $id_name = 'lm-diem-path';
        $val     = esc_attr( get_option( $id_name ) );

        echo '<input type="text" class="input long-input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
        echo '<span class="description">' . __( 'Diem sciript\'s directory path', 'lmwp' ) . '</span>';
    }

    public static function outputPythonPath()
    {
        $id_name = 'lm-python-path';
        $val     = esc_attr( get_option( $id_name ) );

        echo '<input type="text" class="input long-input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
        echo '<span class="description">' . __( 'Python path', 'lmwp' ) . '</span>';
    }
}

