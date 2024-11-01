<?php
//
/* Generate page with list of streams */
//
function list_of_youtube_streams()
{
    global $wpdb;
    $ni_youtube_page = new NI_YouTube();
    $stream = new YoutubeStream();
    $db = new YouDB($wpdb);
    $some_arr = $db->get_streams_from_db();
    if ($_GET['id'])
    {

        $id_youtube = $stream->check_for_int($_GET['id']);
        if (!$some_arr)
        {
            wp_die('Wrong URL adress! There is no such page!');
        }
        if($stream->check_for_int($_GET['delete']) == 1)
        {
            $db->delete_row ($id_youtube);
            ?>
            <META HTTP-EQUIV="refresh" content="0;URL=/wp-admin/admin.php?page=ny_youtube_list">
            <?php
        }

    }
    foreach ($some_arr as $obj)
    {
        settype($obj, 'array');
        $some_arr_new[] = $obj;
    }
    $ni_youtube_page->youtube_search_data = $some_arr_new;
    $ni_youtube_page->prepare_items();
    include_once(PLUGINPATH.'/views/youtube_cron_list_streams.tmpl.php');
}

 ?>