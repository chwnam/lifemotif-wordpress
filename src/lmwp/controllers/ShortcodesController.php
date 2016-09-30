<?php
namespace lmwp\controllers;

use lmwp\services\LifeMotifArticleService;


class ShortcodesController
{
    /**
     * Shortcode callback must "return" what to display because the result is replacing the shortcode.
     * Do not just echo them and use this wrapper function.
     *
     * @param string     $methodName method name to invoke.
     * @param null|array $args       argument from shortcode callback.
     *
     * @return string
     */
    public function callForShortcode($methodName, $args = null)
    {
        if (method_exists($this, $methodName)) {
            ob_start();
            if ($args === null) {
                call_user_func(array($this, $methodName));
            } else {
                call_user_func(array($this, $methodName), $args);
            }

            return ob_get_clean();
        }

        return '';
    }

    public function displayLifeMotif()
    {
        if ( ! is_user_logged_in()) {
            printf('<a href="%s">로그인하세요</a>', esc_url(wp_login_url($_SERVER['REQUEST_URI'])));

            return;
        }

        $articleService = new LifeMotifArticleService();

        // CSS Style
        wp_enqueue_style(
            'lm-ui-datepicker',
            \lmwp\getAssetsUrl() . '/css/jquery-ui-themes-1.11.4/themes/base/jquery-ui.min.css',
            array(),
            '1.11.4'
        );

        // built-in datepicker UI
        wp_enqueue_script('jquery-ui-datepicker');

        // my script
        wp_register_script(
            'lm-datepicker',
            \lmwp\getAssetsUrl() . '/js/lm-datepicker.js',
            array('jquery', 'jquery-ui-datepicker'),
            false,
            true
        );
        wp_localize_script(
            'lm-datepicker',
            'dpObject',
            array(
                'firstArticleDate' => get_option('lm-first-article-date'),
                'articles'         => $articleService->getArticlesOfMonth(),
                'ajaxUrl'          => admin_url('admin-ajax.php'),
                'nonce'            => wp_create_nonce('lm-datepicker-nonce'),
            )
        );
        wp_enqueue_script('lm-datepicker');

        wp_localize_jquery_ui_datepicker();
        \lmwp\getTemplate('lifemotif', array());
    }
}
