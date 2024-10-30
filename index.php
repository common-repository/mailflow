<?php

/**
 * @package Mailflow
 */
/*
  Plugin Name: Mailflow
  Plugin URI: https://mailflow.com/support/wordpress
  Description: Mailflow makes it easy to create beautiful email sequences in moments. Use the Mailflow plugin to connect your Wordpress site to your Mailflow account. You can create customisable forms, add new site members directly to your email campaigns and trigger emails depending on what pages are visited.
  Version: 1.0
  Author: Mailflow
  Author URI: http://mailflow.com/
  License: XYZ
  Text Domain: mailflow
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ){
	die;
}

//Defining constant for use throughout the site
define('MAILFLOW_VERSION', '1.0');
define('MAILFLOW_MIN_WP_REQUIRED', '3.2');
define('MAILFLOW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MAILFLOW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MAILFLOW_TPL', MAILFLOW_PLUGIN_DIR . '/tpl/');
define('MAILFLOW_ASSETS_URL', MAILFLOW_PLUGIN_URL . 'assets/');

require_once 'inc/classes/mailflow.php'; //main front class


//only if admin is logged in then include the admin class
if (is_admin()) {
    require_once 'inc/classes/mailflow.admin.php';
}


//activation
function mailflow_activate() {
    update_option('mailflow-doubleopt-form', 1);
    update_option('mailflow-doubleopt-roles', 0);
}
register_activation_hook( __FILE__, 'mailflow_activate' );