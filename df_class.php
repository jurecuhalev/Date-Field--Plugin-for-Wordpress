<?php

class mph_datefield {
	
	// Settings for each box. Passed by an array, stored in the database.
	function define_box( $df_boxes ) {
		
		$this->id = $df_boxes['id'];
		$this->title = $df_boxes['title'];
		$this->description = $df_boxes['description'];
		$this->post_types = $df_boxes['post_types'];
		$this->fields = $df_boxes['fields'];
		
	}
	
	// Create Fields
	function df_create_fields() {
		
		// Temp - values currently stored in array - until i set up proper post_meta storage.
		global $df_values, $post; 
		
		/* Lazy Admin Style */
		echo '<style>#df_box_'. $this->id .' select{height:20px;padding:0;} #df_box_'.$this->id.' input{ padding:1px;} #df_box_'.$this->id.' label{display:block;margin-bottom:5px;} #df_box_'.$this->id.' div.df_input {margin-bottom:10px; } .df_input {padding: 6px 0;} .df_input label { margin-bottom: 6px; display: block; padding: 0 6px; } .df_input .text_input { width: 98%; }</style>';
		
		echo '<p>' . $this->description . '</p>';
		
		$noncename = 'name_of_nonce_field' . $this->id;
		wp_nonce_field( 'date_field' . MPH_DATEFIELD_VERSION, $this->id . '_nonce' );
		
		foreach( $this->fields as $field ) {
			$value = get_post_meta( $post->ID, '_' . $this->df_field_basename($field['name']), true );
			echo '<div class="df_input">';
			echo '<label><strong>' . $field['name'] . '</strong></label>';
			
			switch ( $field['type'] ) {
				case 'date':
					date_input( $value, $this->df_field_basename($field['name']) ); 	
					break;
				default:
					echo '<input type="text" name="' . $this->df_field_basename($field['name']) . '" value="' . $value . '" class="text_input" />';
					break;
			}
			
			echo '</div>';
		}
	
	}
	
	function df_create() {
		add_action('add_meta_boxes', $this->df_create_box() );
	}
	
	function df_display() {
		$this->df_create_fields();
	}
	
	/* A more usable version of the field name */
	function df_field_nicename( $field ) {
		$field = strtolower( $field );
		//$field = str_replace( ' ', '-', $field );
		$field = str_replace( " ", "-", $field );
		return  $field;
	}
	
	/* Base name used for input name attributes. */
	function df_field_basename( $field ) {
		return $this->id . '_' . $this->df_field_nicename( $field );
	}
	
	function df_update_timestamp( $field, $df_longtime=false ) {
		
		if( $df_longtime ) {
			$this->timestamp[$field] = mktime( $df_longtime['hour'] , $df_longtime['minute'] , 0 , $df_longtime['month'] , $df_longtime['day'] , $df_longtime['year'] );
		} else {
			$this->timestamp = false;
		}
		
	}
	
}

?>