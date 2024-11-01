<?php
//
/* Functions for Database and WP_Cron() */
//
	global $ni_yotube_cron_plugin;
	$ni_youtube_cron_plugin_version = "1.0";
	//
	/* This one is used when plugin is activated */
	//
	function ni_youtube_cron_plugin_install()
   {
	   global $wpdb;
	   global $ni_yotube_cron_plugin_version;
	   $table_name = $wpdb->prefix . "ni_youtube_cron_plugin";
	   if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")!=$table_name)
	   {
		  	$sql = "CREATE TABLE $table_name
		  	(
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  title text DEFAULT '',
			  author text DEFAULT '',
			  tags text DEFAULT '',
			  adding_tags text DEFAULT '',
			  amount int DEFAULT 5,
			  category text DEFAULT '',
			  youtube_ids text DEFAULT '',
			  use_wp_cron TINYINT(1) DEFAULT NULL,
			  UNIQUE KEY id (id)
		    );";
		   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		   dbDelta($sql);
		}

	}
	//
	/* This is when deactivated */
	//
    function ni_youtube_cron_plugin_remove ()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "ni_youtube_cron_plugin";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")==$table_name)
        {
            $wpdb->query(
               "DROP TABLE $table_name"
            );
        }
        wp_clear_scheduled_hook('youtube_hourly_event');
    }
   class YouDB {

   		private $table_name;

   	  public function __construct($wpdb)  
   		{  
   		    $this->table_name = $wpdb->prefix . "ni_youtube_cron_plugin";
   		 		$this->wpdb_you = $wpdb;
   		} 
			/* Get all streams from database */
			//
			public function get_streams_from_db () {
				$streams = $this->wpdb_you->get_results("SELECT * FROM $this->table_name");
				return $streams;
			}
			//
			/* Delete row */
			//
			public function delete_row ($id_youtube) {
				   $this->wpdb_you->query(
		          $this->wpdb_you->prepare(
		              "DELETE FROM $this->table_name
		              WHERE id = $id_youtube"
		            )
		         );
			}
			//
			/* Select row */
			//
			public function select_row ($id_youtube) {
				$row = $this->wpdb_you->get_row("SELECT * FROM $this->table_name WHERE id = $id_youtube");
		    if (!$row)
		    {
		      wp_die('Wrong URL adress! There is no such page!');
		    }
		    settype($row, 'array');
				return $row;
			}
			//
			/* Update a row in youtube table */
			//
			public function update_row_youtube ($id, $title, $author, $tags, $adding_tags, $amount, $category, $youtube_ids, $use_wp_cron) {
				$rows_affected_ni = $this->wpdb_you->update ($this->table_name, array(
		        'title'     => $title,
		        'author'    => $author,
		        'tags'      => $tags,
		        'adding_tags' => $adding_tags,
		        'amount'    => $amount,
		        'category'  => $category,
		        'youtube_ids' => $youtube_ids,
		        'use_wp_cron' => $use_wp_cron,
		     	), array('id' => $id)
				);
		    return true;
			}
			//
			/* Insert a row in youtube table */
			//
			public function insert_row_youtube ($title, $author, $tags, $adding_tags, $amount, $category, $youtube_ids, $use_wp_cron) {
				$rows_affected_ni = $this->wpdb_you->insert ($this->table_name, array(
		        'title'     => $title,
		        'author'    => $author,
		        'tags'      => $tags,
		        'adding_tags' => $adding_tags,
		        'amount'    => $amount,
		        'category'  => $category,
		        'youtube_ids' => $youtube_ids,
		        'use_wp_cron' => $use_wp_cron,
		     	)
				);
			}
	}
