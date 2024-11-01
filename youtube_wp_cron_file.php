<?php
	global $wpdb;
	nocache_headers();
  $table_name = $wpdb->prefix . "ni_youtube_cron_plugin";
  $some_arr = $wpdb->get_results("SELECT * FROM $table_name");
 	$stream = new YoutubeStream();	
  if (!$some_arr)
  {
    wp_die('Wrong URL adress! There is no such page!');
  }
  settype($some_arr, 'array');
  foreach ($some_arr as $obj)
  {
    settype($obj, 'array');
    if ($obj["use_wp_cron"]) 
    {
      $stream->standart_cron($obj, $wpdb);
	  }
  }
