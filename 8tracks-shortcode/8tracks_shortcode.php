<?php

/*
Plugin Name: 8tracks Radio: Official Shortcode Plugin
Plugin URI: http://wordpress.org/extend/plugins/8tracks-shortcode/
Description: Allows you to embed 8tracks playlists via a shortcode.
Version: 1.33
Author: Jonathan Martin
Author URI: http://www.shh-listen.com
License: GPL2 (http://www.gnu.org/licenses/gpl-2.0.html)
*/

/*  Copyright 2011-16  Jonathan Martin  (email : jon@songsthatsavedyourlife.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*  A huge thanks to Justin S, WordPress.com Developer, and Matthew Cieplak at 8tracks.com, for their enormous assistance with the plugin!
*/

/* Usage: [8tracks url ="" height="some value" width="some value" playops="some value(s)" flash="yes/no" tags="your, favorite, genres" collection="yes/no"]

Note:    height, width, and playops are optional. You must specify either a URL, some tags, a dj, an artist, or a particular collection or mix set.
height:      Pick a number, any number.  Standard for single mixes is 250, and 500 for collections.
width:       Yep, pick a number.  Standard is 300 for single mixes, and 500 for collections.
playops:     Can be set to "shuffle", "autoplay", or "shuffle+autoplay".
flash:       (Deprecated, Feb. 2016. Silently drops option for now, and uses HTML5 player.)
tags:        Use this if you want to explore by genre. Simply insert a comma-separated list of tags, and you'll get a random mix.
usecat:      Set to yes to use the WP category name(s) as your search tags on 8tracks.
usetags:	 Set to yes to use the WP Post's tags as your search tags on 8tracks
meta_url:    Use a specific post for usecat and usetags.
artist:      Use this if you want to search for mixes with a given artist.
dj:          Use this to specify a particular user/dj on 8tracks - name or URL is fine.
similar:     Use like URL.  However, instead of a single mix, you get a collection of mixes similar to the one you supplied.
smart_id:    This allows you to copy a smart id from the 8tracks site in order to generate a collection.
sort:        Can be combined with tags or artist, or used on its own. Options are "recent", "hot", or "popular".
*/

//Some useful global values for retrieving mixes.
//8tracks API Stuff
define( 'api_key', '?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
define( 'api_version', '&api_version=3' );

//Begin Custom Editor Button
function tcustom_addbuttons() {
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;

if ( get_user_option('rich_editing') == 'true') {
    add_filter("mce_external_plugins", "add_tcustom_tinymce_plugin");
    add_filter('mce_buttons', 'register_tcustom_button');
}}

function register_tcustom_button($buttons) {
    array_push($buttons, "|", "eighttracks_button");
    return $buttons;
}

function add_tcustom_tinymce_plugin($plugin_array) {
    $plugin_array['eighttracks_button'] = plugins_url().'/8tracks-shortcode/8tracks.js';
    return $plugin_array;
}

add_action('init', 'tcustom_addbuttons');
//End Custom Editor Button

function add_widget_script() {
    wp_enqueue_script( 'widgets.php', plugins_url().'/8tracks-shortcode/widget.js');
}

add_action('admin_enqueue_scripts', 'add_widget_script');
// init process for button control

add_shortcode( "8tracks", "eighttracks_shortcode" );

function eighttracks_shortcode( $atts, $content) {
    extract( shortcode_atts ( array(
        'height' => '',
        'width' => '',
        'playops' => '',
        'url' => NULL,
        'flash' => 'no',
        'tags' => NULL,
        'artist' => NULL,
        'dj' => NULL,
        'collection' => '',
        'smart_id' => NULL,
        'sort' => NULL,
        'lists' => '',
        'is_widget' => '',
        'usecat' => 'no',
        'usetags' => 'no',
        'similar' => NULL,
        'meta_url' => NULL,
        'stub' => 's',
        'are_we_ssl' => '',
        ), $atts, '8tracks' ) );

// <------------- This is the beginning of the variable creation and input sanitization section. -------------->

//If anything other than a URL is defined, you probably want a collection.
    if (isset($url)) {
        $collection = "no";
}
    else {
        $collection = "yes";
}

//It's either a widget, or it's not.
$allowed_widget_options = array(
    'yes',
    'no',
    );

if ( !in_array( $is_widget, $allowed_widget_options ) )
    $is_widget = 'no';

// Let's set the default width parameter. We'll check the validity of the supplied value via regex.
    if (preg_match("/^([0-9]+(%?)$)/", $width)) {
        $width = $width;
}
    else if (($is_widget=="yes") && ($collection=="yes")) {
        $width = '100%';
}
    else if (($is_widget=="no") && ($collection=="yes")) {
        $width = 500;
}
    else if (($is_widget=="yes") && ($collection=="no")) {
        $width = '100%';
}
    else {
        $width = 300;
}

// Now for the height parameter.  We check this the same way as width.
    if (preg_match("/^([0-9]+(%?)$)/", $height)) {
        $height = $height;
}
    else if (($is_widget=="yes") && ($collection=="yes")) {
        $height = 500;
}
    else if (($is_widget=="yes") && ($collection=="no")) {
        $height = 300;
}
    else if (($is_widget=="no") && ($collection=="yes")) {
        $height = 500;
}
    else if (($is_widget=="no") && ($collection=="no")) {
        $height = 250;
}

// Make sure that a user can only enter a whitelisted set of playops.
$allowed_playops = array(
    'shuffle',
    'autoplay',
    'shuffle+autoplay',
    );

if ( !in_array( $playops, $allowed_playops ) )
    $playops = '';

//Tweak the playops for collections:
    if ($playops=="shuffle" || $playops=="autoplay") {
        $options = '&options=' . ($playops) . '';
        $playops = '/' . ($playops) . '';
}
    else if ($playops=="shuffle+autoplay") {
        $options = "&options=shuffle,autoplay";
        $playops = '/' . ($playops) . '';
}

// Make sure flash has a value. Default is no.
    if (isset($flash))
        $flash="no";

// Make sure the URL we are loading is from 8tracks.com
    if (isset($url)) {
        $url_bits = parse_url( $url );
        if ( '8tracks.com' != $url_bits['host'] )
            return '';
}

// Make sure our meta_url is from the same domain as the plugin host. (Won't work else.)
    if (isset($meta_url)) {
        $url_bits = parse_url( $meta_url );
        $domain_name = $_SERVER['SERVER_NAME'];
        if ( $domain_name != $url_bits['host'] )
            return '';
}

//Make sure our sort values are valid.
$allowed_sorts = array(
    'recent',
    'hot',
    'popular',
    );

if ( !in_array( $sort, $allowed_sorts ) ) {
    $sort = '';
}

//Make sure our list settings are valid.
$allowed_lists = array(
    'liked',
    'listen_later',
    'listened',
    'recommended',
    );

if ( !in_array( $lists, $allowed_lists ) )
    $lists = '';

//Make sure that usecat and usetags are set to something valid.

$allowed_usecat_options = array(
    'yes',
    'no',
    );

if ( !in_array( $usecat, $allowed_usecat_options ) )
    $usecat = 'no';

$allowed_usetags_options = array(
    'yes',
    'no',
    );

if ( !in_array( $usetags, $allowed_usetags_options ) )
    $usetags = 'no';

//  <----------- This is the end of the variable creation and input santization section. ------------>

//  <----------- This is the beginning of the section where we format the data to be sent to 8tracks.com ------------>
//Check for SSL, and make sure requests are formatted accordingly:

$allowed_stubs = array( //Make sure we can only have an https or http part in our URL.
    's',
    '',
  );

$are_we_ssl = is_ssl(); //Is the mix/collection being shown on an SSL page, or not?
    if ($are_we_ssl == True) {
      $stub = "s"; //add this 's' to the http in the mix processing below.
}
    else if ($are_we_ssl == False) { //carry on. No change needed to the base URL.
      $stub = "";
}
if ( !in_array( $stub, $allowed_stubs ) ) //enforce the above choices.
    $stub = "";

//These arrays contain character substitutions to ensure the URLs are well-formed for querying 8tracks.
$badchars = array('_', ' ', '/', '.', ',');
$goodchars = array('__', '_', '\\', '^', '+');

//These very similar arrays handle character substitutions from WordPress category/tag names for lookups. (Note the hyphen.)
$badmetachars = array('_', ' ', '/', '.', ',', '-', '\'');
$goodmetachars = array('__', '_', '\\', '^', '+', '_', '');


//We should probably make sure our smart_id is free of non-id elements before processing.
$needle1 = "http://8tracks.com/mix_sets/";
$needle2 = "/collections/";
$needle3 = "https://8tracks.com/mix_sets/";

    if ((strpos($smart_id, $needle1)) !== false) {
        $smart_id = str_replace("http://8tracks.com/mix_sets/", "", $smart_id);
}
    if ((strpos($smart_id, $needle3)) !== false) {
        $smart_id = str_replace("https://8tracks.com/mix_sets/", "", $smart_id);
}

//Collection URLs on 8tracks only return JSON. Here, we get that JSON, extract the collection's smart_id, and pass that back to the plugin.
    if ((strpos($smart_id, $needle2)) !== false) {
        $json_body = wp_remote_get( esc_url($smart_id) . '.json' . (api_key) . '' );
        $json_data = json_decode($json_body['body'], true);
        $smart_id = $json_data["collection"]["smart_id"];
}

//We'll also make sure that any DJ URLs are stripped down to just the DJ's ID.
$dj_needle = "http://8tracks.com/";
$dj_needle2 = "https://8tracks.com/";

    if ((strpos($dj, $dj_needle)) !== false) {
        $dj = str_replace("http://8tracks.com/", "", $dj);
        $dj = preg_replace('/&amp;/i', '-', $dj);  //Replace the string '&amp;' with '-' to account for Tiny_MCE formatting.
        $dj = preg_replace('/(@|\(|\)|\{|\})/i', '', $dj);  //8tracks drops a bunch of characters from DJ URLs.  Doing that here.
        $dj = preg_replace("/[^(a-zA-Z0-9)|(\-)|(\_)]/i", '-', $dj); //Replace all remaining non-aplhanumeric characters with a "-".
}
    if ((strpos($dj, $dj_needle2)) !== false) {
        $dj = str_replace("https://8tracks.com/", "", $dj);
        $dj = preg_replace('/&amp;/i', '-', $dj);  //Replace the string '&amp;' with '-' to account for Tiny_MCE formatting.
        $dj = preg_replace('/(@|\(|\)|\{|\})/i', '', $dj);  //8tracks drops a bunch of characters from DJ URLs.  Doing that here.
        $dj = preg_replace("/[^(a-zA-Z0-9)|(\-)|(\_)]/i", '-', $dj); //Replace all remaining non-aplhanumeric characters with a "-".
    }


//Let's do some mix set processing:
    if (is_null($url)) {

//Did we specify a sort?  Let's make sure that works.
    if ((in_array( $sort, $allowed_sorts )) && ((isset($tags)) || (isset($artist)) || (isset($dj)) || (isset($usecat)) || (isset($usetags)))) {
        $sort = ':' . ($sort) . '';
}

//Here, we create an array to hold known good 8tracks tags, and tags that return zero mixes.  We'll use this to speed-up lookups.

$valid_cat_meta = (get_site_transient( '8tracks_meta_cat_search_results'));
$bad_cat_meta = (get_site_transient( '8tracks_meta_empty_cat_search_results'));
$valid_tag_meta = (get_site_transient( '8tracks_meta_tag_search_results'));
$bad_tag_meta = (get_site_transient( '8tracks_meta_empty_tag_search_results'));

//Here, we convert the WordPress category values to tags parameters:
	if ($usecat=="yes") {
        if ($recentcat=="yes") {
            $categories = get_the_category();
    }
        else if (($is_widget=="yes") && (!is_null($meta_url))) {
            $post_id = url_to_postid(esc_url( $meta_url ));
            $categories = get_the_category( $post_id );
    }
        else if (($is_widget=="yes") && (is_null($meta_url))) {
            //Widget will be created based on the categories of the most recent post.
            $recent_posts_arguments = array('numberposts' => 1, 'post_status' => 'publish');
            $last = wp_get_recent_posts( $recent_posts_arguments );
            $last_id = $last['0']['ID'];
            $categories = get_the_category($last_id);
    }
        else if (($is_widget=="no") && (!is_null($meta_url))) {
            $post_id = url_to_postid( $meta_url );
            $categories = get_the_category( $post_id );
    }
        else if (($is_widget=="no") && (is_null($meta_url))) {

            $categories = get_the_category();
    }

		$separator = ',';
		$valid_cats = array();
		if($categories) {
			foreach($categories as $category) {
				//Let's see if we've already looked up this category before.

				if (in_array(str_replace($badmetachars, $goodmetachars, $category->cat_name), $valid_cat_meta)) {
					$valid_cats[] = ($category->cat_name);
					continue;
				}
				if (in_array(str_replace($badmetachars, $goodmetachars, $category->cat_name), $bad_cat_meta)) {
					print '<!--8tracks Plugin Says: Sorry, but "' . $category->cat_name . '" occurs in zero mixes on 8tracks.com.--> ';
					continue;
				}

				//Test to see whether the categories even exist on 8tracks as tags.
				$json_test = wp_remote_get ( esc_url('http' . $stub .'://8tracks.com/explore/' . str_replace($badmetachars, $goodmetachars, $category->cat_name) . '.json' .'' . (api_key) . '' . (api_version) . ''));
				$json_data = json_decode($json_test['body'], true);

				//If they exist, we add the categories to our valid_cats variable and to valid_meta (for saving for later).
				if ($json_data["total_entries"]) { //Total entries only exists in returned JSON that has status of 200 and a set of mixes to draw from.
					$valid_cat_meta[] = ($category->cat_name);
					$valid_cats[] = ($category->cat_name);
				}

				//If they don't exist, we add them to the array of known invalid tags and also insert an html comment that says so.
				else if (!$json_data["total_entries"]) { //No total entries means the search was empty.
					$bad_cat_meta[] = ($category->cat_name);
				}
			}
		}
        //We need to deal with a case where tags and usecat are both set.
        if (!is_null($tags)) {
            $buffer = explode(',', $tags); //Create an array from the user-supplied string $tags.
            $catsbuffer = array_unique(array_merge($valid_cats, $buffer)); //Merge that array with $valid_tags
    }   else {
            $catsbuffer = $valid_cats;
	}
		//We now pass valid_cats to the tags variable, and process as if they were tags all along.
		$tags = implode(',', $catsbuffer); //Convert the merged array back into a string.

		//We'll store the search data for one day.
		set_site_transient( '8tracks_meta_cat_search_results', (array_unique($valid_cat_meta)), 60*60*24 );
		set_site_transient( '8tracks_meta_empty_cat_search_results', (array_unique($bad_cat_meta)), 60*60*24 );
}

//Here, we convert the WordPress post tags values to tags parameters:
	if ($usetags=="yes") {
        if ($recenttags=="yes") {
            $categories = get_the_tags();
    }
        else if (($is_widget=="yes") && (!is_null($meta_url))) {
            $post_id = url_to_postid(esc_url( $meta_url ));
            $wp_tags = get_the_tags( $post_id );
    }
        else if (($is_widget=="yes") && (is_null($meta_url))) {
            //Widget will be created based on tags of the most recent post.
            $recent_posts_arguments = array('numberposts' => 1, 'post_status' => 'publish');
            $last = wp_get_recent_posts( $recent_posts_arguments );
            $last_id = $last['0']['ID'];
            $wp_tags = get_the_tags($last_id);
    }
        else if (($is_widget=="no") && (!is_null($meta_url))) {
            $post_id = url_to_postid( $meta_url );
            $wp_tags = get_the_tags( $post_id );
    }
        else if (($is_widget=="no") && (is_null($meta_url))) {

            $wp_tags = get_the_tags();
    }

		$separator = ',';
		$valid_tags = array();
        if($wp_tags) {
			foreach($wp_tags as $wp_tag) {
				//Let's see if we've already looked up this tag before.
				if (in_array(str_replace($badmetachars, $goodmetachars, $wp_tag->name), $valid_tag_meta)) {
					$valid_tags[] = ($wp_tag->name);
					continue;
				}

				if (in_array(str_replace($badmetachars, $goodmetachars, $wp_tag->name), $bad_tag_meta)) {
					print '<!--8tracks Plugin Says: Sorry, but "' . $wp_tag->name . '" occurs in zero mixes on 8tracks.com.--> ';
					continue;
				}

				//Test to see whether the tags even exist on 8tracks as tags.
				$json_test = wp_remote_get ( esc_url('http' . $stub .'://8tracks.com/explore/' . str_replace($badmetachars, $goodmetachars, $wp_tag->name) . '.json' .'' . (api_key) . '' . (api_version) . ''));
				$json_data = json_decode($json_test['body'], true);

				//If they exist, we add the categories to our valid_tags variable and to valid_meta (for saving for later)..
				if ($json_data["total_entries"]) { //Total entries only exists in returned JSON that has status of 200 and a set of mixes to draw from.
					$valid_tag_meta[] = ($wp_tag->name);
					$valid_tags[] = ($wp_tag->name);
				}

				//If they don't exist, we add them to the array of known invalid tags and also insert an html comment that says so.
				else if (!$json_data["total_entries"]) { //No total entries means the search was empty.
					$bad_tag_meta[] = ($wp_tag->name);
				}
			}
		}
		//We need to deal with a case where tags and usetags are both set.
        if (!is_null($tags)) {
            $buffer = explode(',', $tags); //Create an array from the user-supplied string $tags.
            $tagsbuffer = array_unique(array_merge($valid_tags, $buffer)); //Merge that array with $valid_tags
    }   else {
            $tagsbuffer = $valid_tags;
    }

        //We now pass valid_tags to the tags variable, and process as if they were tags all along.
		$tags = implode(',', $tagsbuffer); //Convert the merged array $tags into a string.

		//We'll store the search data for one day.
		set_site_transient( '8tracks_meta_tag_search_results', (array_unique($valid_tag_meta)), 60*60*24 );
		set_site_transient( '8tracks_meta_empty_tag_search_results', (array_unique($bad_tag_meta)), 60*60*24 );
}

//Here, we deal with both usecat and usetags being turned on.
	if (($usecat=="yes") && ($usetags=="yes")) {
		$valid_combined_meta = array_unique(array_merge($tagsbuffer, $catsbuffer)); //We combine the arrays containing the valid categories and tags.
		$tags = implode(',', $valid_combined_meta); //We set tags search equal to all the valid categories and post tags.
	}

//Here, we create a smart_id that will return a collection of similar mixes (as determined by Echo Nest) to the mix given.
	if (!is_null($similar)) {
		$the_body = wp_remote_get( esc_url($similar) . '.xml' .'' . (api_key) . '' . (api_version) . '' );

		//Error handling for mix processing.
		if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
			return '';

		if ( ! isset( $the_body['body'] ) )
			return '<!-- invalid response -->';

		try {
			$xml = new SimpleXMLElement( $the_body['body'] );
		}
		catch ( Exception $e ) {
			return '<!-- invalid xml -->';
		}
		$smart_id = 'similar:' . intval($xml->mix->id) . '';
}

//  <---------- This is the end of the data formatting section. --------->

//Here, we create the smart id from tags or artist:
    if ((!is_null($tags)) && (!is_null($sort))) {   //Tag searches with a specified sort.
        $smart_id = 'tags:' . $tags . '' . ($sort) . '';
}
    else if ((!is_null($tags)) && (is_null($sort))) {   //Tag searches without a specified sort.
        $smart_id = 'tags:' . $tags . '';
}
    if ((isset($artist)) && (!is_null($sort))) {   //Artist searches with a specified sort.
        $smart_id = 'artist:' . $artist . '' . ($sort) . '';
}
    else if ((isset($artist)) && (is_null($sort))) {   //Artist searches without a specified sort.
        $smart_id = 'artist:' . $artist . '';
}
    if (isset($dj)) {   //DJ searches.
        $dj = preg_replace('/&amp;/i', '-', $dj);  //Replace the string '&amp;' with '-'.
        $dj = preg_replace('/(@|\(|\)|\{|\})/i', '', $dj);  //8tracks drops a bunch of characters from DJ URLs.  Doing that here.
        $dj = preg_replace("/[^(a-zA-Z0-9)|(\-)|(\_)]/i", '-', $dj); //Replace all remaining non-aplhanumeric characters with a "-" to account for Tiny_MCE formatting.
}

//This handles collections made from smart_id, dj, or sort.

    if (!is_null($smart_id)) {
        $the_body = wp_remote_get ('http' . $stub .'://8tracks.com/mix_sets/' . str_replace($badchars, $goodchars, $smart_id) . '.xml' . (api_key) . '' );
}
    else if (!empty($dj)) {
        $the_body = wp_remote_get ('http' . $stub .'://8tracks.com/' . str_replace($badchars, $goodchars, $dj) . '.xml' . (api_key) . '' );
}
    else if (!empty($sort)) {   //This handles collections where only sort is set.
        if ($sort != "recent") {
            $the_body = wp_remote_get ('http' . $stub .'://8tracks.com/mix_sets/all:' . ($sort) . '.xml' . (api_key) . '' );
    }   else if ($sort == "recent") { //The set of new mixes has to be gotten in a slightly different manner... hence, the include in the next line.
            $the_body = wp_remote_get ('http' . $stub .'://8tracks.com/mix_sets/all:' . ($sort) . '.xml?include=mixes&api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
    }
}

//Error handling for URL processing.
    if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
        return '';

    if ( ! isset( $the_body['body'] ) )
        return '<!-- invalid response -->';

    try {
        $xml = new SimpleXMLElement( $the_body['body'] );
}
    catch ( Exception $e ) {
        return '<!-- invalid xml -->';
}

//Collection processing:

    if (!empty($artist)) {
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com/mix_sets/' . str_replace($badchars, $goodchars, $smart_id) . '/player?platform=wordpress' . ($options) . '" ';
        $output .= 'width="' . ($width) .'" height="' . ($height) . '" ';
        $output .= 'border="0" style="border: 0px none;"></iframe></div>';
}
    else if (!is_null($tags)) {
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com/mix_sets/' . str_replace($badchars, $goodchars, $smart_id) . '/player?platform=wordpress' . ($options) . '" ';
        $output .= 'width="' . ($width) .'" height="' . ($height) . '" ';
        $output .= 'border="0" style="border: 0px none;"></iframe></div>';
}
    else if ((!empty($smart_id)) && (empty($dj)) && (empty($artist))) { //This handles smart-ids (as distinct from DJs).
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com' . ($xml->path) . '/player?platform=wordpress' . ($options) . '" ';
        $output .= 'width="' . ($width) .'" height="' . ($height) . '" ';
        $output .= 'border="0" style="border: 0px none;"></iframe></div>';
}
    else if (!empty($sort)) { //This handles meta lists.  That is: new, trending, or popular.
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com/mix_sets/all:' . ($sort) . '/player?platform=wordpress' . ($options) . '" ';
        $output .= 'width="' . ($width) .'" height="' . ($height) . '" ';
        $output .= 'border="0" style="border: 0px none;"></iframe></div>';
}
    else if ((!empty($lists)) && (!empty($dj))) {  // This is a collection made from lists (recent, popular, etc.).
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com/mix_sets/' . ($lists) . ':' . intval($xml->user->id) . '/player?platform=wordpress' . ($options) . '" ';
        $output .= 'width="' . ($width) .'" height="' . ($height) . '" ';
        $output .= 'border="0" style="border: 0px none;"></iframe></div>';
}
    else if (!empty($dj)) {  //This handles DJs.
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com/mix_sets/dj:' . intval($xml->user->id) . '/player?platform=wordpress' . ($options) . '" ';
        $output .= 'width="' . ($width) .'" height="' . ($height) . '" ';
        $output .= 'border="0" style="border: 0px none;"></iframe></div>';
}

}

//This is for single mix processing:
    if (!is_null($url)) {
        $the_body = wp_remote_get( esc_url($url) . '.xml' .'' . (api_key) . '' . (api_version) . '' );

//Error handling for URL processing.
    if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
        return '';

    if ( ! isset( $the_body['body'] ) )
        return '<!-- invalid response -->';

    try {
        $xml = new SimpleXMLElement( $the_body['body'] );
}
    catch ( Exception $e ) {
        return '<!-- invalid xml -->';
}

//Output a mix where URL is set and HTML5 is turned on.
    if (!is_null($url)) {
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http' . $stub .'://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3_universal' . $playops .'?platform=wordpress" ';
        $output .= 'width="' .($width) . '" height="' . ($height) . '" style="border: 0px none;"></iframe></div>';
}

}

$output = apply_filters('eighttracks_shortcode', $output, $atts);
    if ( $output != '' )
        return $output;
}

//Include Widget Code

include_once dirname( __FILE__ ) . '/widget.php';

//Include Meta Widget Code

include_once dirname( __FILE__ ) . '/meta-widget.php';

//Add Admin Menu Pointers to help new users.

include_once dirname( __FILE__ ) . '/pointer.php';

?>
