<?php
// if uninstall.php is not called by WordPress, die
if (!defined('ABSPATH')) {
    die;
}

$option_name = 'spa_setting_options';

delete_option($option_name);

// for site options in Multisite
delete_site_option($option_name);
