<div class="wrap">
        <div id="icon-users" class="icon32"><br/></div>
        <h2>Add new YouTube stream</h2>
		<br>
     	<form id="video-stream" method="post">
             <table>
                 <tr>
                     <td><label for="ny_youtube_search">Title: </label></td>
                     <td><input name='ny_youtube_search' type='text' value='<?php if(!empty($some_arr["title"])) echo $some_arr["title"]; ?>'/></td>
                 </tr>
                <tr>
                     <td> <label for="ny_youtube_search_author">Author: </label></td>
                     <td><input name='ny_youtube_search_author' type='text' value='<?php if(!empty($some_arr["author"])) echo $some_arr["author"];?>'/></td>
                 </tr>
                 <tr>
                     <td><label for="ny_youtube_search_tags">Search tags: </label></td>
                     <td><input name='ny_youtube_search_tags' type='text' value='<?php if(!empty($some_arr["tags"])) echo $some_arr["tags"];?>'/></td>
                 </tr>
                 <tr>
                 	<td><label for="ny_youtube_adding_tags">Adding tags:</label></td>
                 	<td><input type="text" name="ny_youtube_adding_tags" value="<?php if(!empty($some_arr["adding_tags"])) echo $some_arr["adding_tags"];?>"></td>
                 </tr>
                 <tr>
                 <tr>
                 	<td><label for="ny_youtube_wp_cron">Use WP Cron (hourly)</label></td>
                 	<td><input type="checkbox" name="ny_youtube_wp_cron" value="1" <?php if($some_arr["use_wp_cron"]) echo "checked"; ?> /></td>
                 </tr>
                 </tr>
                 <tr>
                     <td><label for="ny_youtube_search_amount">Amount of maximum video result:</label></td>
                     <td><input name='ny_youtube_search_amount' type='text' value='<?php if(!empty($some_arr["amount"])) { echo $some_arr["amount"];} else  { echo "5"; }?>'/></td>
                 </tr>
                 <tr>
                     <td><label for="select_name">Choose category to post in</label></td>
                     <td><?=$stream->get_categories_multi($some_arr["category"])?></td>
                 </tr>
             </table>
            <p class="submit">
                <input  type="submit" name="submit" class="button-primary" value="<?=$submit_text?>">
            </p>
        </form>
    </div>