<?php
/**
 * Date Field widget class
 *
 * @since 2.8.0
 */
class df_widget extends WP_Widget {
	
	function df_widget() {
		$widget_ops = array('classname' => 'Date Field Widget', 'description' => __( 'Posts queried by date field meta') );
		$this->WP_Widget('date_field', __('Date Field'), $widget_ops);
	}
	
	/** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;		
		$args = array(
			'meta_key' => $instance['field'],
			'meta_compare' => $instance['compare']
		);
		$this->output($args);
		echo $after_widget;
    }

	function update($new_instance, $old_instance) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['field'] = strip_tags($new_instance['field']);
		$instance['compare'] = strip_tags($new_instance['compare']);
		$instance['post_type'] = strip_tags($new_instance['post_type']);
        return $instance;
	}

	/** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $field = esc_attr($instance['field']);
        $compare = esc_attr($instance['compare']);
        $post_type = esc_attr($instance['post_type']); 
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('field'); ?>"><?php _e('Field:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('field'); ?>" name="<?php echo $this->get_field_name('field'); ?>" type="text" value="<?php echo $field; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" type="text" value="<?php echo $post_type; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('compare'); ?>"><?php _e('Compare:'); ?>
			<select id="<?php echo $this->get_field_id('compare'); ?>" name="<?php echo $this->get_field_name('compare');?>">
				<option value="past" <?php if( $compare == 'past' ) { echo 'selected="selected"'; } ?> >Past</option>
				<option value="future" <?php if( $compare == 'future' ) { echo 'selected="selected"'; } ?> >Future</option>
			</select>
			</label></p>
        <?php 
    }

	
	function output( $args = false ) {
			
		if($args['meta_compare'] == 'future' ) {
			$args['meta_compare'] = '>';
		} else {
			$args['meta_compare'] = '<';
		}
		
		$defaults = array(
		   'post_type'=>'post',
		   'meta_key' => '_df_1_start',
		   'meta_compare' => '>',
		   'meta_value' => time(),
		
		   'order' => 'ASC'
		);
	
		$args = array_merge( $defaults, $args);
		
		$date_query = new WP_Query($args);
		if ($date_query->have_posts()) : ?>
			<ul>
			<?php while ($date_query->have_posts()) : $date_query->the_post(); global $post; ?>
				<li><a href="<?php the_permalink(); ?>">
					<?php echo date('d/m/y', get_post_meta( $post->ID, $args['meta_key'], true) ); ?> - <?php the_title(); ?>
				</a></li>
			<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p>No upcoming events</p>
		<?php endif;

		// RESET THE QUERY
		wp_reset_query();
	}
	
}

add_action('widgets_init', create_function('', 'return register_widget("df_widget");'));
?>