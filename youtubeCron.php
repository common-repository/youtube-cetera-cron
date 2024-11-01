<?php
/*
Plugin Name: YouTube Cetera Cron
Plugin URI: http://wordpress.org/extend/plugins/youtube-cetera-cron/
Description: YouTube Cetera Cron made for synchronizing Wordpress sites with your favorite YouTube channels.
Version: 1.0
Author: ceteralabs
Author URI: http://cetera.ru
=======================================================================================================================================
*/
register_activation_hook(__FILE__,'ni_youtube_cron_plugin_install');
add_action('youtube_hourly_event', 'cron_wp_youtube');
	if ( !wp_next_scheduled( 'youtube_hourly_event' ) ) {
		wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'youtube_hourly_event');
	}
define(PLUGINPATH, plugin_dir_path(__FILE__));
require_once('functions/page_functions.php');
function cron_wp_youtube () {
	include 'youtube_wp_cron_file.php';
}
register_deactivation_hook(__FILE__, 'ni_youtube_cron_plugin_remove');
add_action('admin_menu', 'youtube_cron_ni_menu');