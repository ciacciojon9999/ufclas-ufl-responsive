<?php

class UFCOM_recent_posts extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'widget_ufcom_recent_posts sidebar_widget', 'description' => 'Your most recent posts' );
		parent::__construct('UFCOM_recent_posts', 'Recent Posts', $widget_ops);
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
   
		/**
		 * Check whether the widget should only be shown on a certain page
		 */
		global $wp_query;
		$current_page = $wp_query->post->ID;
		$unique_page_id = ( isset($instance['unique_page_id']) )? $instance['unique_page_id']:null;
		
		if( !empty($unique_page_id) && is_page( $current_page ) ){
			$unique_page_content = get_page_by_title($unique_page_id);
			if( $current_page != $unique_page_content->ID ){
				return false;	
			}
		}
		
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$numberofposts = empty($instance['numberofposts']) ? '&nbsp;' : apply_filters('widget_numberofposts', $instance['numberofposts']);
		$showexcerpt = isset( $instance['showexcerpt'] ) ? $instance['showexcerpt'] : false;
		$showthumbnails = isset( $instance['showthumbnails'] ) ? $instance['showthumbnails'] : false;
		$showdate = isset( $instance['showdate'] ) ? $instance['showdate'] : false;
		$showrssicon = isset( $instance['showrssicon'] ) ? $instance['showrssicon'] : false;

		$featured_content_category = of_get_option("opt_featured_category");
		$specific_category_id = isset( $instance['specific_category_id'] ) ? $instance['specific_category_id']:'';
		$specific_category_id_actual = '';
		if ( !empty($specific_category_id) ) {
			$specific_category_id_actual = "&cat=";
			$specific_category_id_actual .= get_cat_id($specific_category_id);
		}
		
		$showrssiconimage = '';
		if ($showrssicon=="on"){
				$iconpath = get_bloginfo('template_url') . '/images/rss.png';
				$showrssiconimage = "<a href='".get_bloginfo('rss2_url')."'><img class='rss-icon' src='" . $iconpath . "' class='rss_icon' alt='Subscribe to RSS Feed'/></a> ";
		}
 
		if ( !empty( $title ) ) { echo $before_title . $title . $showrssiconimage . $after_title; };

		$recentPosts = new WP_Query();
		$recentPosts->query("showposts=".$numberofposts."&cat=-".$featured_content_category.$specific_category_id_actual."");
			while ($recentPosts->have_posts()) : $recentPosts->the_post();
			
   
	
			$margin = '';
			echo "<div id='recent-posts' class='news-announcements'><div class='item'>";
				if ($showthumbnails) {
					if((ufandshands_post_thumbnail('thumbnail', 'alignleft', 130, 100))) {
					  //$margin = "margin-160";
					}
				}  
				
				$margin_bottom =(empty($showexcerpt))? 'margin_bottom_none':'';				
					
				echo "<h4><a href=\"".get_permalink()."\">".get_the_title()."</a></h4>";
				if ($showdate){ echo "<p class='time {$margin} {$margin_bottom}'>".get_the_time('M jS, Y')."</p>"; }
				if ($showexcerpt) { echo "<p>". get_the_excerpt() ."</p>"; }
				
			echo "</div></div>";
			endwhile;

			wp_reset_query();
		
		echo $after_widget;

	}
 
	function update($new_instance, $old_instance) {
		$instance = array_merge( $old_instance, $new_instance );
		
		$strip_tags = array('title', 'numberofposts');
		
		foreach( $instance as $key => $value ){
			if( ! array_key_exists($key, $new_instance) ){
				$instance[$key] = "";
			}
			elseif( in_array( $key, $strip_tags) ){
				$instance[$key] = strip_tags($value);
			}
		}
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Recent News', 'numberofposts' => '5', 'showexcerpt' => 'on', 'showthumbnails' => true, 'showdate' => true, 'showrssicon' => true, 'unique_page_id' => '', 'specific_category_id' => '' ) );
		$title = strip_tags($instance['title']);
		$numberofposts = strip_tags($instance['numberofposts']);
		$showexcerpt = $instance['showexcerpt'];
		$showthumbnails = $instance['showthumbnails'];
		$showdate = $instance['showdate'];
		$showrssicon = $instance['showrssicon'];

		$unique_page_id = $instance['unique_page_id'];

		$specific_category_id = $instance['specific_category_id'];
	
?>

			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>

			<p><label for="<?php echo $this->get_field_id('numberofposts'); ?>">Number of posts: <input class="widefat" id="<?php echo $this->get_field_id('numberofposts'); ?>" name="<?php echo $this->get_field_name('numberofposts'); ?>" type="text" value="<?php echo esc_attr($numberofposts); ?>" /></label></p>
			
			<p><input class="checkbox" type="checkbox" <?php checked( $instance['showdate'], 'on' ); ?> id="<?php echo $this->get_field_id( 'showdate' ); ?>" name="<?php echo $this->get_field_name( 'showdate' ); ?>" /> &nbsp; <label for="<?php echo $this->get_field_id( 'showdate' ); ?>">Show post dates?</label></p>

			<p><input class="checkbox" type="checkbox" <?php checked( $instance['showexcerpt'], 'on' ); ?> id="<?php echo $this->get_field_id( 'showexcerpt' ); ?>" name="<?php echo $this->get_field_name( 'showexcerpt' ); ?>" /> &nbsp; <label for="<?php echo $this->get_field_id( 'showexcerpt' ); ?>">Show post excerpt?</label></p>

			<p><input class="checkbox" type="checkbox" <?php checked( $instance['showthumbnails'], 'on' ); ?> id="<?php echo $this->get_field_id( 'showthumbnails' ); ?>" name="<?php echo $this->get_field_name( 'showthumbnails' ); ?>" /> &nbsp; <label for="<?php echo $this->get_field_id( 'showthumbnails' ); ?>">Show post thumbnails?</label></p>

			<p><input class="checkbox" type="checkbox" <?php checked( $instance['showrssicon'], 'on' ); ?> id="<?php echo $this->get_field_id( 'showrssicon' ); ?>" name="<?php echo $this->get_field_name( 'showrssicon' ); ?>" /> &nbsp; <label for="<?php echo $this->get_field_id( 'showrssicon' ); ?>">Show RSS icon next to title?</label></p>

<p>
				<label for="<?php echo $this->get_field_id( 'specific_category_id' ); ?>">From the category:</label>
				<select id="<?php echo $this->get_field_id( 'specific_category_id' ); ?>" name="<?php echo $this->get_field_name( 'specific_category_id' ); ?>" class="widefat" style="width:100%;">
					<option value="">
					<?php echo esc_attr__('All categories'); ?></option> 
					<?php 
					$categories = get_categories('hide_empty=0&orderby=name');
					foreach ($categories as $category_specific) {
						$title = $category_specific->cat_name;
						$option = '<option ';
						if ($title == $instance['specific_category_id']) {
							$option .= ' selected="selected" >';
						} else {
							$option .= ' >';
						}
						$option .= $category_specific->cat_name;
						$option .= '</option>';
						echo $option;
				 	 }
				 	?>
				</select>
			</p>




			<p>
				<label for="<?php echo $this->get_field_id( 'unique_page_id' ); ?>">Display only on page:</label>
				<select id="<?php echo $this->get_field_id( 'unique_page_id' ); ?>" name="<?php echo $this->get_field_name( 'unique_page_id' ); ?>" class="widefat" style="width:100%;">
					<option value="">
					<?php echo esc_attr__('All pages'); ?></option> 
					<?php 
					$pages = get_pages(); 
					foreach ($pages as $pagg) {
						$title = $pagg->post_title;
						$option = '<option ';
						$option .= 'value="'.htmlspecialchars($title).'" ';
						if ($title == $instance['unique_page_id']) {
							$option .= ' selected="selected" >';
						} else {
							$option .= ' >';
						}
						$option .= $title;
						$option .= '</option>';
						echo $option;
				 	 }
				 	?>
				</select>
			</p>

<?php
	}
}

register_widget( 'UFCOM_recent_posts' );

?>