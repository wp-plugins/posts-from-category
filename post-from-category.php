<?php
/*
 * Plugin Name: Post from Category
 * Version: 1.0.0
 * Plugin URI: #
 * Description: Plugin to display posts from specific category. 
 * Author: Manesh Timilsina
 * Author URI: http://manesh.com.np/
 * License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */


$dir = plugin_dir_path( __FILE__ );

require_once($dir.'functions.php');

class PFCWidget extends WP_Widget {
	/**
	* Declares the PFCWidget class.
	*
	*/
	

	function PFCWidget(){
		global $control_ops, $post_cat, $post_num, $post_length;

		$widget_ops = array(						
						'classname' => 'pfc-widget', 
						'description' => __( "Display posts from selected category") 
						);
		
		$this->WP_Widget('PFCWidget', __('Posts From Category'), $widget_ops, $control_ops);
	}
	
	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance){

		extract($args);

		$title 			= apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);

		$post_cat 		= ! empty( $instance['post_cat'] ) ? $instance['post_cat'] : false;

		$post_order_by	= ! empty( $instance['post_order_by'] ) ? $instance['post_order_by'] : false;

		$post_order		= ! empty( $instance['post_order'] ) ? $instance['post_order'] : false;

		$post_num 		= ! empty( $instance['post_cat'] ) ? $instance['post_num'] : 5;

		$post_length 	= ! empty( $instance['post_length'] ) ? $instance['post_length'] : 10;

		$post_exclude 	= ! empty( $instance['post_exclude'] ) ? $instance['post_exclude'] : '';

		$date			= ! empty( $instance['date'] ) ? '1' : '0';

		$read_more		= ! empty( $instance['read_more'] ) ? '1' : '0';

		$thumbnail		= ! empty( $instance['thumbnail'] ) ? '1' : '0';

		$post_thumbs	= ! empty( $instance['post_thumbs'] ) ? $instance['post_thumbs'] : false;	

		
		?>        
		<?php echo $before_widget; ?>
				<?php if ( $title ) echo $before_title . $title . $after_title; ?>
			<?php 
			$exclude_id = explode(',', $post_exclude);
			$p_args = array( 
						'cat' 				=> $post_cat, 
						'orderby' 			=> $post_order_by, 
						'order' 			=> $post_order, 
						'post_status' 		=> 'publish',
						'posts_per_page' 	=> $post_num,
						'post__not_in' 		=> $exclude_id 
					);
			
			$p_query = new WP_Query( $p_args );

			if($p_query->have_posts()){

				while($p_query->have_posts()){

					$p_query->the_post(); ?>
					<h2>
						<a href="<?php the_permalink(); ?>" title="<?php _e('Go to '.get_the_title(), 'PFC') ?>"><?php the_title(); ?></a>
					</h2>
					<?php if( 1 == $date ){  ?>
						<span><?php echo get_the_date(); ?></span>
						<div style="clear:both;"></div>
					<?php } ?>

					<?php 
					if( 1 == $thumbnail ){

						if(has_post_thumbnail()) { ?>

							<a href="<?php the_permalink(); ?>" title="<?php _e('Go to '.get_the_title(), 'PFC') ?>">
							<?php the_post_thumbnail( $post_thumbs ); ?>
							</a>
							<div style="clear:both;"></div>

							<?php } 

					}
					?>
					
					<p><?php echo custom_limit_words(sanitize_text_field(get_the_content()), $post_length); ?></p>

					<?php
					if( 1 == $read_more ){ ?>
						<a href="<?php the_permalink(); ?>" title="<?php _e('Go to '.get_the_title(), 'PFC') ?>" class="read-more">Read More</a>
					<?php } ?>
					<?php 

				}
				wp_reset_query();

			}

			?>
			
		<?php echo $after_widget; ?>
	<?php
	}	
	
	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance){	
		
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'facebook_page_url'=>'', 'twitter_id'=>'', 'gplus_id'=>'') );
		
		
		$title 			=  isset( $instance['title'] ) ? $instance['title'] : '';	

		$post_cat 		= isset( $instance['post_cat'] ) ? $instance['post_cat'] : '';

		$post_order_by	= isset( $instance['post_order_by'] ) ? $instance['post_order_by'] : '';

		$post_order 	= isset( $instance['post_order'] ) ? $instance['post_order'] : '';

		$post_num		= isset( $instance['post_num']) ? $instance['post_num'] : 5;	

		$post_length	= isset( $instance['post_length']) ? $instance['post_length'] : 10;

		$post_exclude	= isset( $instance['post_exclude']) ? $instance['post_exclude'] : '';

		$date 			= isset( $instance['date'] ) ? (bool) $instance['date'] : false;
		
		$read_more 		= isset( $instance['read_more'] ) ? (bool) $instance['read_more'] : false;

		$thumbnail 		= isset( $instance['thumbnail'] ) ? (bool) $instance['thumbnail'] : false;

		$post_thumbs 	= isset( $instance['post_thumbs'] ) ? $instance['post_thumbs'] : '';

			
		
		# Output the options?>
		<p style="text-align:left;">
		<label for="<?php echo $this->get_field_name('title'); ?>">
		<?php _e('Title:'); ?></label>
		<input style="width: 100%;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		
		</p>
		
		<p style="text-align:left;">
			<label for="<?php echo $this->get_field_id('post_cat'); ?>">
			<?php _e('Select Category:'); ?>
			</label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id('post_cat'); ?>" name="<?php echo $this->get_field_name('post_cat'); ?>">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
		<?php
			$cats = get_categories( array( 'hide_empty' => 0 ) );
			foreach ( $cats as $cat ) {
				echo '<option value="' . $cat->term_id . '"'
					. selected( $post_cat, $cat->term_id, false )
					. '>'. esc_html( $cat->name ) . '</option>';
			}
		?>
			</select>
		</p>

		<p style="text-align:left;">
			<label for="<?php echo $this->get_field_id('post_order_by'); ?>">
			<?php _e('Order By:'); ?>
			</label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id('post_order_by'); ?>" name="<?php echo $this->get_field_name('post_order_by'); ?>">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
		<?php
			$orders = array( 
							'author' => 'Author',
							'title' => 'Post Title',
							'ID' => 'Post ID',
							'date' => 'Date',							
							'menu_order' => 'Menu Order',						
							'comment_count' => 'Number of Comments',
							'rand' => 'Random'
						);
			foreach ( $orders as $key => $value ) {
				echo '<option value="' . $key . '"'
					. selected( $post_order_by, $key, false )
					. '>'. esc_html( $value ) . '</option>';
			}
		?>
			</select>
		</p>

		<p style="text-align:left;">
			<label for="<?php echo $this->get_field_id('post_order'); ?>">
			<?php _e('Order:'); ?>
			</label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id('post_order'); ?>" name="<?php echo $this->get_field_name('post_order'); ?>">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
		<?php
			$p_orders = array( 
							'ASC' => 'Ascending',
							'DESC' => 'Descending',
						);
			foreach ( $p_orders as $key => $value ) {
				echo '<option value="' . $key . '"'
					. selected( $post_order, $key, false )
					. '>'. esc_html( $value ) . '</option>';
			}
		?>
			</select>
		</p>

		<p style="text-align:left;">
		<label for="<?php echo $this->get_field_name('post_num'); ?>">
		<?php _e('Number of Post:'); ?></label>
		<input style="width: 100%;" id="<?php echo $this->get_field_id('post_num'); ?>" name="<?php echo $this->get_field_name('post_num'); ?>" type="number" value="<?php echo $post_num; ?>" />
		
		</p>

		<p style="text-align:left;">
		<label for="<?php echo $this->get_field_name('post_exclude'); ?>">
		<?php _e('Exclude Posts:'); ?></label>
		<input style="width: 100%;" id="<?php echo $this->get_field_id('post_exclude'); ?>" name="<?php echo $this->get_field_name('post_exclude'); ?>" type="text" value="<?php echo $post_exclude; ?>" />
		<small>Enter post id separated with comma to exclude multiple posts.</small>
		
		</p>

		<p style="text-align:left;">
		<label for="<?php echo $this->get_field_name('post_length'); ?>">
		<?php _e('Words of Excerpt:'); ?></label>
		<input style="width: 100%;" id="<?php echo $this->get_field_id('post_length'); ?>" name="<?php echo $this->get_field_name('post_length'); ?>" type="number" value="<?php echo $post_length; ?>" />
		
		</p>

		<p>		  
		 	<input class="checkbox" type="checkbox" <?php echo checked( $thumbnail ); ?> id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" /> 
		 	<label for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e('Display/Select Thumbnail'); ?></label>		    
		    <select style="width: 100%; margin-top:10px;" id="<?php echo $this->get_field_id('post_thumbs'); ?>" name="<?php echo $this->get_field_name('post_thumbs'); ?>">	    		
		    <?php
		    	$thumbs = array( 
		    					'thumbnail' => 'Thumbnail',
		    					'medium' => 'Medium',
		    					'large' => 'Large',
		    					'full' => 'Full',
		    				);
		    	foreach ( $thumbs as $key => $value ) {
		    		echo '<option value="' . $key . '"'
		    			. selected( $post_thumbs, $key, false )
		    			. '>'. esc_html( $value ) . '</option>';
		    	}
		    ?>
		    	</select>
		</p>

		<p>
		<input class="checkbox" type="checkbox" <?php checked( $date ); ?> id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'date' ); ?>">
		<?php _e( 'Display Post Date' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php echo checked( $read_more ); ?> id="<?php echo $this->get_field_id('read_more'); ?>" name="<?php echo $this->get_field_name('read_more'); ?>" /> 
			<label for="<?php echo $this->get_field_id('read_more'); ?>"><?php _e('Display Read More'); ?></label>			
		</p>		

		

		<?php		
	
	
	
	} //end of form

	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){

		$instance 					= $old_instance;

		$instance['title'] 			= strip_tags(stripslashes($new_instance['title']));		
		
		$instance['post_cat'] 		= (int) $new_instance['post_cat'];

		$instance['post_order_by'] 	= $new_instance['post_order_by'];

		$instance['post_order'] 	= $new_instance['post_order'];

		$instance['post_num'] 		= (int) $new_instance['post_num'];

		$instance['post_length'] 	= (int) $new_instance['post_length'];

		$instance['post_exclude'] 	= $new_instance['post_exclude'];

		$instance['date'] 			= $new_instance['date'] ? 1 : 0;

		$instance['read_more'] 		= $new_instance['read_more'] ? 1 : 0;

		$instance['thumbnail'] 		= $new_instance['thumbnail'] ? 1 : 0;		

		$instance['post_thumbs'] 	= $new_instance['post_thumbs'];

		
		
		return $instance;
	}
	
	

	
	}// END class
	
	/**
	* Register  widget.
	*
	* Calls 'widgets_init' action after widget has been registered.
	*/
	function pfcwidget_init() {
		register_widget('PFCWidget');
	}	
	add_action('widgets_init', 'pfcwidget_init');
	
		
	 
?>