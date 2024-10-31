<?php
/**
 * Post To Sidebar Widget.
 * Adds a widget to display selected posts in a sidebar.
 *
 * 
 */


/**
 * Post To Sidebar Widget Class
 */
class post_to_sidebar_widget extends WP_Widget {

	/** constructor */
    function post_to_sidebar_widget() {
        parent::WP_Widget(false, $name = 'Post To Sidebar');
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title; ?>
		<?php get_post_to_sidebar_data();?>
		<?php echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}


} // class post_to_sidebar_widget


/////////////////////////////////////////////////////////////
//
//Looks for the posts that have been set to appear on THIS page.
//If posts are found, add has-sidebar class name to body tag.
//
////////////////////////////////////////////////////////////
function post_to_sidebar_check($classes){
	global $wpdb;
	global $post;
	global $post_to_sidebar;

	//Get the array of custom post types and convert to string for use in query
	$args=array(
		  'public'   => true,
		  '_builtin' => false
		); 
	$output = 'names'; // names or objects
	$operator = 'and'; // 'or' or 'or'
	$post_types=get_post_types($args,$output,$operator);
	$post_types_string = implode("','",$post_types);
	$post_types_string = "'post','" . $post_types_string . "'";

	$p2s_string_length = strlen($post->post_name);
	$search_string = $p2s_string_length . ':"' . $post->post_name;

	//query the database
	$post_to_sidebar = $wpdb->get_results( "
		SELECT * FROM $wpdb->posts
		LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
		WHERE $wpdb->postmeta.meta_key = 'post_to_sidebar_pageTitle'
		AND $wpdb->postmeta.meta_value LIKE '%$search_string%'
		AND $wpdb->posts.post_status = 'publish'
		AND $wpdb->posts.post_type IN ($post_types_string)
		ORDER BY $wpdb->posts.menu_order ASC
		" );

	if ( $post_to_sidebar ) {
		$classes[] = 'has-sidebar';
	}

	return $classes;
}

add_filter( 'body_class', 'post_to_sidebar_check' );



//Display the sidebar posts if there are any.
function get_post_to_sidebar_data(){
	global $post_to_sidebar;
	global $post;

	if ($post_to_sidebar) {
		foreach ($post_to_sidebar as $post):
		setup_postdata($post);
		
		?>
		<div class="post-to-sidebar">
	
			<?php the_title('<h3 class="widget-title">', '</h3>'); ?>
			
				<div class="sidebar-entry-content">
					<?php the_content(); ?>
					<?php edit_post_link('Edit', ' | ', ' | '); ?>
				</div>
		</div>
		<?php
		endforeach;
		rewind_posts();?>
	<?php
	}
}

//Hide the title if option is set
function post_to_sidebar_title_check($title) {
	global $post;
	if ( !is_admin() && get_post_meta( $post->ID, 'post_to_sidebar_title', true ) ){
		
		$title = '';
	}
	return $title;
}
	
add_filter( 'the_title', 'post_to_sidebar_title_check' );


//Show the excerpt if option is set
function post_to_sidebar_excerpt_check($content) {
	global $post;
	if ( !is_admin() && get_post_meta( $post->ID, 'post_to_sidebar_excerpt', true ) ){
		$content = $post->post_excerpt;

		// If an excerpt is set in the Optional Excerpt box
		if( $content ){
			$content = apply_filters('the_excerpt', $content);
		}
		
	}
	return $content;
}
	
add_filter( 'the_content', 'post_to_sidebar_excerpt_check' );

// register Post_To_Sidebar_Widget widget
add_action( 'widgets_init', create_function('', 'return register_widget("post_to_sidebar_widget");'));
?>