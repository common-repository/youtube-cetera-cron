=== Plugin Name ===
Contributors: ceteralabs
Donate link: http://cetera.ru
Tags: youtube, cron, cetera
Requires at least: 2.2
Tested up to: 3.4.1
Stable tag: 3.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
This is plugin for reposting youtube videos on your site. Uses linux cron or wp_cron function and YouTube API v.2. It's pretty simple in everyday use.  

[Plugin Developers page](http://cetera.ru/ "cetera") 

== Installation ==

1. Upload youtube-cetera-cron folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure you can upload images in the media uploader.
4. Set up cron schedule for example like that "*/60 * * * *	root	/usr/bin/wget -O /dev/null -q http://somesite.com/wp-content/plugins/youtube-cetera-cron-plugin/cronPlugin.php" or you can just check the wp_cron option and it's will be working too.

== Frequently Asked Questions ==

= How add my stream to the site =

That's very simple. You should go to the 'Add new YouTube stream' page and fill out the form. There are several fields which you can use to search through youtube videos:

 1. Title - title of the video.
 2. Author - name of youtube channel.
 3. Search tags - search via tags.
 4. Adding tags - tags that will add to each video that stream post.
 5. Use wp_cron - option that turn wp_cron function (in case you don't want to schedule yourself).
 6. Amount of video - amount of video you want to search through each time.
 7. Categories - you can choose multiple categories.

If you do everything correctly your stream will be start to work immediatly.

== Screenshots ==

1. screenshot-1.jpg is list of streams page.
2. screenshot-2.jpg is add stream page.

== Changelog ==

= 1.0 =
This is the first version of this plugin

== Upgrade Notice ==

= 1.0 =
This is the first version of this plugin. You should definitely try it out.