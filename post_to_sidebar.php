<?php
/*
Plugin Name: Post To Sidebar
Plugin URI:
Description: A widget that allows post authors to display posts in the sidebar of specified pages.
Version: 1.1.4
Author: David Mallon
Author URI: http://iknowwebdesign.com
License: GPLv2
Requires: 3.0
Original Design Notes: 
	In an Edit Post screen display a multiple select dropdown of all published pages. Selected choices are saved to postmeta.
	When a page is loaded/displayed, if the postmeta value exists dispaly the contents of the included post.
	
*/

/*

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as 
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	02110-1301	USA
*/

/* Launch the plugin */
add_action( 'plugins_loaded', 'post_to_sidebar_setup' );

/**
 * Initialize the plugin.  This function loads the required files needed for the plugin
 * to run in the proper order.
 *
 * @since 1.0
 */
function post_to_sidebar_setup() {

	/* Set constant path to the plugin directory. */
	define( 'post_to_sidebar_DIR', plugin_dir_path( __FILE__ ) );

	/* Load the plugin's widgets. */
	add_action( 'widgets_init', 'post_to_sidebar_load_widgets' );

}

/**
* Loads all the widget files at appropriate time. Calls the register function for each widget
*
* @since 1.0
*/
function post_to_sidebar_load_widgets() {
	require_once( post_to_sidebar_DIR . 'widget_post_to_sidebar.php' );
	register_widget( 'post_to_sidebar_widget' );
}


// backwards compatible
add_action('admin_init', 'post_to_sidebar_add_custom_box', 1);



add_action('admin_menu', 'post_to_sidebar_post_plugin_menu');

function post_to_sidebar_post_plugin_menu() {
	add_options_page('Post To Sidebar Options', 'Post To Sidebar Settings', 'manage_options', 'post-to-sidebar-options', 'post_to_sidebar_plugin_options');
}


/******************************************************/
//
// Print the plugin admin screen
//
/******************************************************/
function post_to_sidebar_plugin_options() {

	if (isset($_POST['post_to_sidebar_submit'])){
	update_option('p2s__use_locations', $_POST['p2s_use_locations']);
	update_option('p2s_use_excerpt', $_POST['p2s_use_excerpt']);
	update_option('p2s_use_title', $_POST['p2s_use_title']);
	update_option('p2s_post_types', implode(',',$_POST['p2s_post_types']));
	}

	if (get_option('p2s_use_locations')) {
	$location_option = ' checked="checked"';
	} else {
        $location_option = '';
	}

	if (get_option('p2s_use_title')) {
	$title_option = ' checked="checked"';
	} else {
        $title_option = '';
	}

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}?>
	<div class="wrap">
	<h2>Post To Sidebar Options</h2> 
	<form id="options_form" method="post" action="">
		<h3>Meta Box Options:</h3>

		<ul>

			<li><input type="checkbox" id="post_to_sidebar_title" name="p2s_use_title" value="yes"<?php echo post_to_sidebar_options_check('p2s_use_title', 'yes', 'checked="checked"');?> />Title<br /><em>Adds an option to hide the post title on output.</em></li>

			<li><input type="checkbox" id="post_to_sidebar_excerpt" name="p2s_use_excerpt" value="yes"<?php echo post_to_sidebar_options_check('p2s_use_excerpt', 'yes', 'checked="checked"');?> />Excerpt<br /><em>Adds an option to show the post excerpt on output.</em></li>
		</ul>

		<h3>Available for these post types:</h3>

		<?php 
		$args=array(
		  'public'   => true,
		  '_builtin' => false
		); 
		$output = 'names'; // names or objects
		$operator = 'and'; // 'or' or 'or'
		$post_types=get_post_types($args,$output,$operator);
		?>

		<ul>
			<li><input type="checkbox" <?php echo post_to_sidebar_options_check('p2s_post_types', 'post', 'checked="checked"');?> name="p2s_post_types[]" value="post" /> post</li>

			<?php
			//loop through any custom post types
			foreach ($post_types  as $post_type ) {
			echo '<li><input type="checkbox"' . post_to_sidebar_options_check('p2s_post_types', $post_type, 'checked="checked"') . ' name="p2s_post_types[]" value="' . $post_type . '" /> ' . $post_type . '</li>';
			}?>
		</ul>
		
		<input type="submit" name="post_to_sidebar_submit" id="post_to_sidebar_submit" value="Save" /></p>
	</form>
	
	</div>
<?php
}


/******************************************************/
//
// Adds a box to the main column on the edit screens
//
/******************************************************/
function post_to_sidebar_add_custom_box() {

    $types = explode(',',get_option('p2s_post_types'));

    foreach ( $types as $type ) {
    add_meta_box( 'post_to_sidebar_sectionid', __( 'Post To Sidebar Options', 'post_to_sidebar_textdomain' ), 
                'post_to_sidebar_inner_custom_box', $type );
    }
}

//******************************************************/
//
// Checks to see if an option is already selected in postsmeta
//
//******************************************************/
function post_to_sidebar_selected_check($check_id, $meta_key, $check_value, $checker)
	{
	$theString = serialize(get_post_meta( $check_id, $meta_key));
	$pos = strpos($theString,$check_value);
	if($pos !== false) {
		return $checker;
		}
		else {
		 return $check_id;
		}
	}

//******************************************************/
//
// Checks to see if an option is already selected in options table
//
//******************************************************/
function post_to_sidebar_options_check($option_name, $check_value, $checker)
	{
	$theString = serialize(get_option( $option_name));
	$pos = strpos($theString,$check_value);
	if($pos !== false) {
		return $checker;
		}
		else {
		 return;
		}
	}


//**********************************************************************************************
//
// End of admin section
//
//**********************************************************************************************


//******************************************************/
//
// Prints the box content
//
//******************************************************/
function post_to_sidebar_inner_custom_box() {
global $post;
  // Use nonce for verification
  wp_nonce_field( plugin_basename(__FILE__), 'post_to_sidebar_noncename' );

	echo '<p>Page this post appears on (ctrl click for multiple select):</p>';
	echo '<select name="page-dropdown[]" multiple="multiple" size="5" style="height:7em">';
	echo '<option value="">'. esc_attr(__('Select page')) . '</option>';

	$pages = get_pages(); 
		foreach ($pages as $pagg) {
  		$option = '<option ' . post_to_sidebar_selected_check($post->ID, 'post_to_sidebar_pageTitle', $pagg->post_name, 'selected="selected"') . ' value="'.$pagg->post_name.'">';
		$option .= $pagg->post_title;
		$option .= '</option>';
		echo $option;
	}

	echo '</select>';
	echo '<br /><br />';
	//Print excerpt checkbox
	if (get_option('p2s_use_excerpt')) {
		echo '<input type="checkbox" ' . post_to_sidebar_selected_check($post->ID, 'post_to_sidebar_excerpt', 'yes', 'checked="checked"') .' name="post-excerpt" value="yes" />Show as excerpt<br />';
		}

	//Print title checkbox
	if (get_option('p2s_use_title')) {
	echo '<input type="checkbox" ' . post_to_sidebar_selected_check($post->ID, 'post_to_sidebar_title', 'no', 'checked="checked"') .' name="post-title" value="no" />Remove title';
		}

	//Print left/right location radio buttons
	if (get_option('p2s_use_locations')) {
		echo '<br /><br />';
		echo '<p>Location:</p>';
		echo '<input type="radio" ' . post_to_sidebar_selected_check($post->ID, 'post_to_sidebar_location', 'side', 'checked="checked"') .' name="post-location" value="side" />Side<br />';
		echo '<input type="radio" ' . post_to_sidebar_selected_check($post->ID, 'post_to_sidebar_location', 'bottom', 'checked="checked"') .' name="post-location" value="bottom" />Bottom<br />';
		}
	

}


//******************************************************/
//
// Save the selected values
//
//******************************************************/
function post_to_sidebar_save_postdata($post_id) {
		if ($_POST['page-dropdown'])
		{
		update_post_meta($post_id, 'post_to_sidebar_pageTitle', $_POST['page-dropdown']);
		update_post_meta($post_id, 'post_to_sidebar_excerpt', $_POST['post-excerpt']);
		update_post_meta($post_id, 'post_to_sidebar_title', $_POST['post-title']);
		if (get_option('p2s_use_locations')) {
			update_post_meta($post_id, 'post_to_sidebar_location', $_POST['post-location']);
			}
		}
	}

// Process the custom values submitted when saving posts.
add_action('save_post', 'post_to_sidebar_save_postdata');




?>