<?php
include_once 'functions.php';
include_once 'db.php';
include_once ABSPATH . 'wp-admin/includes/image.php';
//
/* Generate menu of plugin */
//
function youtube_cron_ni_menu()
{

    add_menu_page( 'YouTube Cron Plugin', 'YouTube Cron Plugin', 'administrator', 'ny_youtube_list', 'list_of_youtube_streams');
    add_submenu_page('ny_youtube_list', 'Add new YouTube Stream', 'Add new YouTube Stream', 'administrator',        'youtube_cron_ni_menu_page', 'youtube_cron_ni_menu_page');
}
//
/* Include pages functionality */
//
include_once PLUGINPATH.'/pages/list_of_youtube_streams.php';
include_once PLUGINPATH.'/pages/add_youtube_stream.php';