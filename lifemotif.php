<?php

/**
 * Plugin Name: LifeMotif
 * Description: Lifemotif wordpress plugin.
 * Version: 0.1.0
 * Author: Changwoo Nam
 * Author URI: cs.chwnam@gmail.com
 */

if ( ! defined('ABSPATH')) {
    return;
}


define('LMWP_MAIN', __FILE__);
define('LMWP_PATH', dirname(__FILE__));

require_once 'src/lmwp/lmwp.php';

$GLOBALS['lmwp'] = \lmwp\Lmwp::getInstance();
