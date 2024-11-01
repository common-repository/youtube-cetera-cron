<?php

//
/* Include tables class file */
//
if(!class_exists('WP_List_Table'))
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
* Youtube stream class
*/
class YoutubeStream
{
    //Getting data from json-youtube
    private function curl ($url) 
    {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($c, CURLOPT_TIMEOUT, 5);
        return json_decode(curl_exec($c));
    }
    //
    /* Stupid function for checking vars (don't know why?) */
    //
    public function check_for_int($id)
    {
        $id = intval($id);
        $reg = "/[0-9]/";
        if (!preg_match($reg,  $id))
        {
            wp_die('Wrong URL adress! There is no such page!');
        }
        else
        {
            return $id;
        }
    }
    //
    /* Multiselect categories */
    //
    public function get_categories_multi ($category) {
         $select_cats = wp_dropdown_categories( array( 'echo' => 0, 'hide_empty' => 0, 'name' => 'select_name', 'selected' => $category , 'hierarchical' => true ) );
         $select_cats = str_replace( 'name=\'select_name\'', 'name=\'select_name[]\' multiple="multiple"', $select_cats);
         if (!empty($category)) {
             foreach ($category as $cat) {
                $select_cats = str_replace( 'value="'.$cat.'"', 'value="'.$cat.'" selected', $select_cats);
             }
         }
         return $select_cats;   
    }
    //
    /* iframe with youtube video link */
    //
    public  function getcontent_for_post ($post_id)
    {
        $post = '<iframe width="420" height="315" src="http://www.youtube.com/embed/'.$post_id.'" frameborder="0" allowfullscreen></iframe>';
        return $post;
    }
    //
    /* Grub video that not in serialized array in database */
    //
    public function grub_video_by_id ($video_id)
    {
        $data = $this->curl('http://gdata.youtube.com/feeds/api/videos/'.$video_id.'?v=2&alt=jsonc');
        settype($data, 'array');
        $data_tmp = $data["data"];
        settype($data_tmp, 'array');
        return $data_tmp;
    }
    //
    /* Retrieving and decoding json youtube-objects */
    //
    public function youtube_video($search, $tags, $author, $max)
    {
        if (!empty($search))
        {   
            $search = urlencode($search);
            $searchq = "q=$search";
        } 
        if (!empty($author))
        {   
            $author = urlencode($author);
            $uploader = "&author=$author";
        }
        if (!empty($tags))
        {   
            $tags = urlencode($tags);
            $tagsq = "&category=$tags";
        } 
        $data = $this->curl('http://gdata.youtube.com/feeds/api/videos?'.$searchq.'&v=2'.$uploader.$tagsq.'&max-results='.$max.'&orderby=published&alt=jsonc');
        $results = array();
        $i=1;
        if ($data->data->items) 
        {
            foreach ($data->data->items as $search_res) 
                {

                    $results[] = array(
                        'ID' => $i++,
                        'title' => $search_res->title,
                        'ID_you' => $search_res->id,
                        'uploader' => $search_res->uploader,
                        'tags' => $search_res->tags,
                        'description' => $search_res->description,
                        'image_url' => $search_res->thumbnail->sqDefault
                    );
                }
                return $results;
        }
    }
    //
    /* Get serialized array */
    //
    public function get_serialized_youtube_data ($array) 
    {
        foreach ($array as $video)
        {
            $serializiedyou_array[] = $video["ID_you"];
        }
        return serialize($serializiedyou_array);
    }
    //
    /* Attach thumbnail to the post */
    //
    public function get_the_thumbnail ($image_url, $post_id, $video_id) 
    {
      $upload_dir = wp_upload_dir();
      $image_data = file_get_contents($image_url);
      $filename = $video_id.basename($image_url);
      if(wp_mkdir_p($upload_dir['path']))
          $file = $upload_dir['path'] . '/' . $filename;
      else
          $file = $upload_dir['basedir'] . '/' . $filename;
          file_put_contents($file, $image_data);
          $wp_filetype = wp_check_filetype($filename, null );
          $attachment = array(
              'post_mime_type' => $wp_filetype['type'],
              'post_title' => sanitize_file_name($filename),
              'post_content' => '',
               'post_status' => 'inherit'
          );
      $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
      $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
      wp_update_attachment_metadata( $attach_id, $attach_data );
      set_post_thumbnail( $post_id, $attach_id );
    }
    public function strip_tags_here ($array) {
        foreach ($array as $key => $post_value)
        {
            if (!is_array($array[$key])) {
                $array[$key] = wp_strip_all_tags($post_value);
            }
        }
        return $array;
    }
    public function standart_cron ($obj, $wpdb) 
    {
        $array_foreach_youtube = $this->youtube_video($obj["title"], $obj["tags"], $obj["author"], $obj["amount"]);
        $youtube_serialized_data = $this->get_serialized_youtube_data ($array_foreach_youtube);
        $unser_array_for_loop = unserialize($obj["youtube_ids"]);
        $ser_array_for_loop = unserialize($youtube_serialized_data);
        $result_you_sov = array_diff ($ser_array_for_loop, $unser_array_for_loop);
        if (!empty($result_you_sov)) 
        {
            $rows_affected_ni = $wpdb->update ($table_name, array(
                'youtube_ids' => $youtube_serialized_data
                ), array('id' => $obj['id']) 
            );
            $result_you_sov = array_reverse($result_you_sov);
            foreach ($result_you_sov as $result_you_id)
            {
                $video_new = $this->grub_video_by_id($result_you_id);
                $category_youtube_to_post_in = unserialize($obj["category"]);
                $single_tags = implode(",", $video_new["tags"]);
                $all_tags = $single_tags.",".$obj["adding_tags"];
                $post = '<iframe width="420" height="315" src="http://www.youtube.com/embed/'.$result_you_id.'" frameborder="0" allowfullscreen></iframe>';
                $youtube_video_post = array(
                    'post_title' => $video_new["title"],
                    'post_content' => $post,
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'tags_input' => $all_tags,
                    'post_category' => $category_youtube_to_post_in,
                    'post_excerpt' => $video_new["description"],
                     "filter" => true
                );
                // Insert the post into the database
                $post_id = wp_insert_post($youtube_video_post);
                $post_with_html = $wpdb->update ($wpdb->posts, array('post_content' => $post), array('ID' => $post_id) );
                $image_url = $video_new["thumbnail"]->sqDefault;
                $this->get_the_thumbnail($image_url, $post_id, $video_new["id"]);
            }
        }
    }
}

//
/* This massive class wp-table generating 'list of youtube' page */
//
class NI_YouTube extends WP_List_Table {

    public $youtube_search_data = array();

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_default($item, $column_name){
        switch($column_name)
        {
            case 'category':
            	$category_string = "";
            	$cats_in_str = unserialize($item[$column_name]);
            	$i = count($cats_in_str);
            	$cn = 1;
            	foreach ($cats_in_str as $category)
            	{	
	            	$category_name = get_the_category_by_ID($category);
	            	if ($i == $cn)
	            		$category_string .= $category_name; 
	            	else 
	            	{
		            	$category_string .= $category_name.", "; 
	            	}
	            	$cn++; 
             	}
                return  $category_string;
            default:
                return $item[$column_name]; //Show the whole array for troubleshooting purposes
        }
    }

    function column_title($item){

        //Build row actions
        $page = 'youtube_cron_ni_menu_page';
        $id = intval($item['id']);
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&id=%d">Edit</a>', $page, $id),
            'delete'    => sprintf('<a href="?page=%s&id=%d&delete=1">Delete</a>',$_REQUEST['page'], $id),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function get_columns(){
        $columns = array(
            'title' => 'Title',
            'author'     => 'Author',
            'tags'    => 'Search tags',
            'adding_tags' => 'Adding tags',
            'amount'   => 'Amount of video',
            'category' => 'Category to post in'

    );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',true),     //true means its already sorted
            'author'     => array('author',false),
            'adding_tags' => array('adding_tags', false),
            'amount'   => array('amount',false),
            'category' => array('category',false)
        );
        return $sortable_columns;
    }

    function prepare_items() 
    {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $data = $this->youtube_search_data;

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        if (is_array($data)) {
            usort($data, 'usort_reorder');

            $current_page = $this->get_pagenum();

            $total_items = count($data);
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
            
        }
        $this->items = $data;
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}


