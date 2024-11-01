<?php
/*
Plugin Name: WP Symposium Blog Post
Plugin URI: http://wordpress.org/extend/plugins/wp-symposium-blogpost/
Description: Integrates WP Symposium with your WordPress blog.
Author: AlphaGolf_fr
Author URI: http://profiles.wordpress.org/AlphaGolf_fr/
License: GPL2
Requires at least: WordPress 3.0, WP Symposium 13.04
Tested up to: 3.9
Stable tag: 1.6.0
Version: 1.6.1
*/

// Is WP Symposium running?
if ( ! function_exists( 'is_plugin_active_for_network' ) || ! function_exists( 'is_plugin_active' ) ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_multisite() )
	(bool)$wps_is_active = ( is_plugin_active_for_network( 'wp-symposium/wp-symposium.php' ) || is_plugin_active( 'wp-symposium/wp-symposium.php' ) );
else
	(bool)$wps_is_active = is_plugin_active( 'wp-symposium/wp-symposium.php' );

// Exit if WPS not running
if ( !$wps_is_active )
	return false;

// Otherwise, check a few defines and go ahead
if ( !function_exists( '__wps__get_url' ) ) include_once( plugin_dir_path( __FILE__ ).'../wp-symposium/functions.php' );
if ( !defined( 'WPS_OPTIONS_PREFIX' ) ) define( 'WPS_OPTIONS_PREFIX', 'symposium' );
if ( !defined( 'WPS_TEXT_DOMAIN' ) ) define( 'WPS_TEXT_DOMAIN', 'wp-symposium' );

include_once( 'wp-symposium-blogpost_functions.php' );
include_once( 'wp-symposium-blogpost_admin.php' );

/* ====================================================================== MAIN =========================================================================== */

function symposium_blogpost_main() {
	
	$html = '<div class="__wps__wrapper">';
		
		// This filter allows others to add text (or whatever) above the output
		$html = apply_filters ( 'symposium_blogpost_filter_top', $html);
		
		// Prepare for the output (which is created via AJAX)
		$html .= '<div id="symposium_blogpost_div">';
		$html .= "<img src='".get_option('symposium_images')."/busy.gif' />";
		$html .= '</div>';
		
	$html .= '</div>';
	
	return $html;
}

/* ===================================================================== ADMIN =========================================================================== */

function symposium_blogpost_activate() {
	
	global $wpdb;
	$roles_with_cap = symposium_blogpost_get_roles_by_cap('edit_posts');
	
	if ( !get_option('symposium_blogpost_nr_of_posts') ) {
		update_option('symposium_blogpost_nr_of_posts', '10');
		update_option('symposium_blogpost_add_post_to_activity', 'on');
		update_option('symposium_blogpost_show_post', $roles_with_cap);
		update_option('symposium_blogpost_rewrite_author_link', 'on');
		
		if ( is_multisite() ) {
			$query = "SELECT blog_id FROM ".$wpdb->base_prefix."blogs ORDER BY blog_id";
			$blog_list = $wpdb->get_results( $query, ARRAY_A );
			foreach ($blog_list as $blog) { $blog_ids[] = $blog['blog_id']; }
			update_option('symposium_blogpost_list_sites', implode(",", $blog_ids));
		}
		
		$anonymous_comment_to_activity = (get_option('comment_moderation') == '1') ? "on" : ""; // Default value mirrors WP comment moderation value
		update_option('symposium_blogpost_anonymous_comment_to_activity', $anonymous_comment_to_activity);
	}
	if ( !get_option('symposium_blogpost_profile_page') ) {
		update_option('symposium_blogpost_profile_page', 'blogpost');
	}
	
	$profile_menu_structure = get_option(WPS_OPTIONS_PREFIX.'_profile_menu_structure');
	$profile_menu_structure_others = get_option(WPS_OPTIONS_PREFIX.'_profile_menu_structure_other');
	if ( !strstr( $profile_menu_structure, '=blogpost' ) && !strstr( $profile_menu_structure_others, '=blogpost' ) ) {
		update_option(WPS_OPTIONS_PREFIX.'_profile_menu_structure', $profile_menu_structure . "\r\n" . __('My Blog Posts', 'wp-symposium-blogpost') . '=blogpost' );
		update_option(WPS_OPTIONS_PREFIX.'_profile_menu_structure_other', $profile_menu_structure_others .  "\r\n" . __('Blog Posts', 'wp-symposium-blogpost') . '=blogpost' );
	}
}
register_activation_hook(__FILE__,'symposium_blogpost_activate');

function symposium_blogpost_deactivate() {

}
register_deactivation_hook(__FILE__, 'symposium_blogpost_deactivate');

function symposium_blogpost_uninstall() {
	
	global $wpdb;
	
	// Delete all options
	$wpdb->query( "DELETE FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'symposium_blogpost_%'" );
}
register_uninstall_hook(__FILE__, 'symposium_blogpost_uninstall');

function symposium_blogpost_init() {
	
	// Load Javascript into WordPress the correct way
	wp_enqueue_script( 'wp-symposium-blogpost', plugins_url( 'js/wp-symposium-blogpost.js', __FILE__ ), array('jquery', '__wps__') );
	
	// Load CSS into WordPress the correct way
	$myStyleUrl = plugins_url( 'css/wp-symposium-blogpost.css', __FILE__ );
	$myStyleFile = plugin_dir_path( __FILE__ ).'css/wp-symposium-blogpost.css';
	if ( file_exists($myStyleFile) ) {
        wp_register_style( 'symposium_blogpost_StyleSheet', $myStyleUrl );
        wp_enqueue_style( 'symposium_blogpost_StyleSheet' );
	}
	
	// Language files
	$plugin_path = dirname(plugin_basename(__FILE__)) . '/lang';
	if ( function_exists('load_plugin_textdomain') ) { load_plugin_textdomain( 'wp-symposium-blogpost', false, $plugin_path ); }
}
add_action('init', 'symposium_blogpost_init');


/* ====================================================== HOOKS/FILTERS INTO WP SYMPOSIUM ====================================================== */

// Add Menu item to Profile Menu through filter provided
// The menu picks up the ID of div and will then run the file that it points to when clicked.
// It will pass $_POST['action'] set to the same as the ID to that file to then be acted upon.
// $uid1 user profile page displayed, $uid2 user displaying the page
function add_blogpost_menu($html,$uid1,$uid2,$privacy,$is_friend,$extended,$share) {  
	
	require_once('wp-symposium-blogpost_functions.php');
	
	(array)$user_role = (array)symposium_blogpost_get_user_roles( $uid1 );
	(array)$show_posts = explode( ",", get_option('symposium_blogpost_show_post', '') );
	(array)$menu_item = get_option('symposium_blogpost_menu_item', array('own_profile' => 'on', 'own_profile_text' => __('My Blog Posts', 'wp-symposium-blogpost'), 'others_profile' => 'on', 'others_profile_text' => __('Blog Posts', 'wp-symposium-blogpost')));
	
	if ( array_intersect( $user_role, $show_posts) ) {
		
		if ($uid1 == $uid2) {
			if ( $menu_item['own_profile'] ) {
				$html .= '<div id="wp-symposium-blogpost" class="__wps__profile_menu">';
				$html .= $menu_item['own_profile_text'];
				$html .= '</div>';
			}
		
		} else {
			if ( $menu_item['others_profile'] ) {
				$html .= '<div id="wp-symposium-blogpost" class="__wps__profile_menu">';
				$html .= $menu_item['others_profile_text'];
				$html .= '</div>';
			}
		}
	}
	return $html;
}
add_filter('__wps__profile_menu_filter', 'add_blogpost_menu', 10, 7);

// Add support for horizonal menu, introduced with WP Symposium v12.11
// Need to add, for example My Blog Posts=blogpost to own menu, on Profile Menu Options
// and Blog Posts=blogpost to other members menu
function add_blogpost_menu_tabs($html,$title,$value,$uid1,$uid2,$privacy,$is_friend,$extended,$share) {  
	
	if ($value == 'blogpost') {

		require_once('wp-symposium-blogpost_functions.php');
		
		(array)$user_role = (array)symposium_blogpost_get_user_roles( $uid1 );
		(array)$show_posts = explode( ",", get_option('symposium_blogpost_show_post', '') );
		
		if ( array_intersect( $user_role, $show_posts) )
			$html .= '<li id="wp-symposium-blogpost" class="__wps__profile_menu" href="javascript:void(0)">'.$title.'</a></li>';

	}
	
	return $html;
}  
add_filter('__wps__profile_menu_tabs', 'add_blogpost_menu_tabs', 10, 9);

// Add row to WPS installation page showing status of the plugin through hook provided
function add_blogpost_installation_row() {
	__wps__install_row(
		'wpblogpost',																								// unique identifier
		__('Blog_Post', WPS_TEXT_DOMAIN), 																			// plugin title (replace spaces with _)
		'symposium-blogpost', 																						// shortcode
		'symposium_blogpost_main',																					// main function
		'-', 																										// internal URL path or -
		'wp-symposium-blogpost/wp-symposium-blogpost.php', 															// main plugin file
		'admin.php?page=wp-symposium-blogpost/wp-symposium-blogpost_admin.php', 									// admin page
		'__wps__activated'																							// set as activated on installation page
	);

}
add_action('__wps__installation_hook', 'add_blogpost_installation_row');

// Add "Blog Posts" to WP Symposium admin menu via hook
function symposium_add_blogpost_to_admin_menu() {
	add_submenu_page(
		'symposium_debug',
		__('Blog Posts', 'wp-symposium-blogpost'),
		__('Blog Posts', 'wp-symposium-blogpost'),
		'manage_options',
		'wp-symposium-blogpost/wp-symposium-blogpost_admin.php',
		'symposium_blogpost_admin_page'
	);
}
add_action('__wps__admin_menu_hook', 'symposium_add_blogpost_to_admin_menu');

// Add text to Activity rows for blog posts and comments
add_filter('__wps__activity_row_item_filter', 'symposium_blogpost_add_to_activity_row', 9, 2);

/* ====================================================== HOOKS/FILTERS INTO WORDPRESS ====================================================== */

// Add shortcode, where shortcode should match 3rd paramter in __wps__install_row()
function wp_symposium_blogpost_shortcode() {

	return __wps__show_profile("blogpost");
}
if (!is_admin()) add_shortcode('symposium-blogpost', 'wp_symposium_blogpost_shortcode');

// For this hook to work, the theme must call 'get_author_posts_url()' in its single.php
function symposium_rewrite_author_link ($link, $author_id, $author_nicename = "") {
	
	if ( ( $profile_url = __wps__get_url('profile') ) && get_option('symposium_blogpost_rewrite_author_link', 'on') ) {
		$author_profile_page = add_query_arg( array('uid' => $author_id ), $profile_url );
		$profile_page_view = get_option('symposium_blogpost_profile_page', 'default');
		if ( $profile_page_view != 'default' ) $author_profile_page = add_query_arg( array('view' => $profile_page_view ), $author_profile_page );
		
		return $author_profile_page;
	
	} else {
		return $link;
	}
}
add_filter('author_link', 'symposium_rewrite_author_link', 10, 3);

// For this hook to work, the theme must call 'comments_template()' in its single.php
function symposium_rewrite_commenter_link ($comments, $post_ID) {

	if ( ( $profile_url = __wps__get_url('profile') ) && get_option('symposium_blogpost_rewrite_commenter_link', '') ) {
		(array)$comments_returned;
		foreach ($comments as $comment) {
			$comment_returned = $comment;
			$commenter = get_user_by('email', $comment->comment_author_email);
			if ( $commenter ) {
				$comment_returned->comment_author_url = add_query_arg( array('uid' => $commenter->ID ), $profile_url );
			}
			$comments_returned[] = $comment_returned;
		}
		return $comments_returned;
	
	} else {
		return $comments;
	}
}
add_filter('comments_array', 'symposium_rewrite_commenter_link', 10, 2);

function symposium_blogpost_publish_post($post_ID) {
	
	global $wpdb;
	
	if (get_option('symposium_blogpost_add_post_to_activity', 'on') != '') {
	
		(array)$post = get_post($post_ID);
		
		if ($post->post_date == $post->post_modified) { // check if it's been just published, or it is republished, to avoid duplicated entries in symposium_comments
			$url = __("Published a new blog post:", 'wp-symposium-blogpost').' <a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
			$sql = "INSERT INTO ".$wpdb->base_prefix."symposium_comments ( subject_uid, author_uid, comment_parent, comment_timestamp, comment, is_group, type ) VALUES ( %d, %d, %d, %s, %s, %s, %s )";
			$success = $wpdb->query( $wpdb->prepare( $sql, $post->post_author, $post->post_author, 0, date("Y-m-d H:i:s"), $url, '', 'blogpost' ) );	
		}
	}
}
add_action('publish_post', 'symposium_blogpost_publish_post');

function symposium_blogpost_publish_comment($comment_ID, $comment_status) {
	
	global $wpdb;
	
	(array)$comment = get_comment($comment_ID);
	(array)$post = get_post($comment->comment_post_ID);
	
	if (get_option('symposium_blogpost_add_'.$post->post_type.'_comment_to_activity', 'on') != '') {
		
		if ( ($comment_status == "approve") || ($comment_status == "1") ) {
			
			// Insert the comment
			symposium_blogpost_add_comment($comment_ID);
		
		} else {
		
			// Delete the comment
			symposium_blogpost_remove_comment($comment_ID);
		}
		
	}
}
add_action('comment_post', 'symposium_blogpost_publish_comment', 20, 2);
add_action('wp_set_comment_status', 'symposium_blogpost_publish_comment', 10, 2);

function symposium_blogpost_edit_comment($comment_ID) {
	
	global $wpdb;
	
	(array)$comment = get_comment($comment_ID);
	
	// Is this comment in Symposium comments table ?
	$comments = $wpdb->get_results("SELECT * FROM ".$wpdb->base_prefix."symposium_comments WHERE subject_uid = '".$comment->user_id."' AND comment_timestamp = '".$comment->comment_date_gmt."'");
	
	// Insert the comment for a registrered user if it isn't already in table and approving it
	if ( ( empty($comments) ) && ( $comment->comment_approved == "1" ) ) { symposium_blogpost_add_comment($comment_ID); }
	
	// Delete the comment if it's in table and de-approving it
	if ( ( $comments ) && ( $comment->comment_approved == "0" ) ) { symposium_blogpost_remove_comment($comment_ID); }
}
add_action('edit_comment', 'symposium_blogpost_edit_comment', 20, 1);

?>
