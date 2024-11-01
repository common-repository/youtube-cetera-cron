<?php
//
/* Generating add stream page */
//
function youtube_cron_ni_menu_page()
{
    global $wpdb; 
    $stream = new YoutubeStream();
    $db = new YouDB($wpdb);
    $submit_text =  'Add new stream';
    if ($_GET['id'])
    {
        $id_youtube = $stream->check_for_int($_GET['id']);
        $some_arr = $db->select_row($id_youtube);
        $submit_text = 'Update stream data';
    }
    if ((!empty($_POST['ny_youtube_search']) || !empty($_POST['ny_youtube_search_author']) || !empty($_POST['ny_youtube_search_tags'])) && !empty($_POST['ny_youtube_search_amount']) )
    {
        extract($stream->strip_tags_here($_POST));
        $ny_youtube_adding_tags = trim($ny_youtube_adding_tags);
        $ny_youtube_wp_cron ? 1 : 0;
        if (!empty($select_name))
        {
            $cat_in_str = serialize($select_name);
        }
        $array_foreach_youtube = $stream->youtube_video($ny_youtube_search, $ny_youtube_search_tags, $ny_youtube_search_author, $ny_youtube_search_amount);
        if (!empty($array_foreach_youtube))
        {
            if(!empty($some_arr))
            {
                $youtube_serialized_data = $stream->get_serialized_youtube_data ($array_foreach_youtube);
                $unser_array_for_loop = unserialize($some_arr["youtube_ids"]);
                $ser_array_for_loop = unserialize($youtube_serialized_data);
                $updated =  $db->update_row_youtube ($some_arr['id'], $ny_youtube_search, $ny_youtube_search_author, $ny_youtube_search_tags, $ny_youtube_adding_tags, $ny_youtube_search_amount, $cat_in_str, $youtube_serialized_data, $ny_youtube_wp_cron);
                $result_you_sov = array_diff ($ser_array_for_loop, $unser_array_for_loop);
                $result_you_sov = array_reverse($result_you_sov);
                foreach ($result_you_sov as $result_you_id)
                {
                           $video_new = $stream->grub_video_by_id($result_you_id);
                           $post_content = $stream->getcontent_for_post($result_you_id);
                           $single_tags = implode(",", $video_new["tags"]);
                           $all_tags = $single_tags.",".$ny_youtube_adding_tags;
                           $youtube_video_post = array(
                                'post_title' => $video_new["title"],
                                'post_content' => $post_content,
                                'post_status' => 'publish',
                                'post_author' => 1,
                                'tags_input' => $all_tags,
                                'post_category' => $select_name,
                                'post_excerpt' => $video_new["description"]
                            );
                            $post_id = wp_insert_post($youtube_video_post); // Insert the post into the database
                            $stream->get_the_thumbnail($video_new["thumbnail"]->sqDefault, $post_id, $result_you_id); // Attach thumbanail to the post
                }
            }
            else
            {
                $youtube_serialized_data = $stream->get_serialized_youtube_data ($array_foreach_youtube);
                $db->insert_row_youtube($ny_youtube_search, $ny_youtube_search_author, $ny_youtube_search_tags, $ny_youtube_adding_tags, $ny_youtube_search_amount, $cat_in_str, $youtube_serialized_data, $ny_youtube_wp_cron);
                $array_foreach_youtube = array_reverse($array_foreach_youtube);
                foreach ($array_foreach_youtube as $video)
                {
                  $post_content = $stream->getcontent_for_post($video["ID_you"]);
                  $ny_youtube_adding_tags = explode(",", $ny_youtube_adding_tags);
                  foreach ($ny_youtube_adding_tags as $tag) {
                     $video["tags"][] = $tag;
                  }
                  $youtube_video_post = array(
                     'post_title' => $video["title"],
                     'post_content' => $post_content,
                     'post_status' => 'publish',
                     'post_author' => 1,
                     'tags_input' => $video["tags"],
                     'post_category' =>  $select_name,
                     'post_excerpt' => $video["description"]
                  );
                  $post_id = wp_insert_post($youtube_video_post); // Insert the post into the database
                  $stream->get_the_thumbnail($video["image_url"], $post_id, $video["ID_you"]);  // Attach thumbanail to the post
                }
            }
        }
    ?>
    <META HTTP-EQUIV="refresh" content="0;URL=/wp-admin/admin.php?page=ny_youtube_list">
    <?php
    }
    if ($updated)
    {
        $some_arr = $db->select_row ($id_youtube);
    }
    if ($some_arr["category"])
    {
      $some_arr["category"] = unserialize($some_arr["category"]);
    }
    include_once(PLUGINPATH.'/views/youtube_cron_add_new.tmpl.php');
}