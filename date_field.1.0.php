<?php
/*
Plugin Name: Date Field
Plugin URI: http://matth.eu/wordpress-date-field-plugin 
Description: Adds a custom field for adding dates to posts. The dates are stored as a unix timestamp. Version 1.0 is NOT COMPATIBLE with previous versions.
Version: 1.1
Author: Matthew Haines-Young
Author URI: http://matth.eu

To Do:
	Look into localization - try gmdate?
	Extend to other fields?
*/

/*	Copyright YEAR  Matthew Haines-Young  (email : mail@matth.eu)

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

	include_once( dirname(__FILE__) . '/df_functons.php' );
	include_once( dirname(__FILE__) . '/df_class.php' );
	include_once( dirname(__FILE__) . '/df_admin.php' );
	include_once( dirname(__FILE__) . '/df_widget.php' );

	define('MPH_DATEFIELD_VERSION', '1.0');
	
	//Initiate mph_datefield class for each defined box.
	if( is_admin() ) :
	
		$df_boxes = get_option('df_date_fields');
	
		if( $df_boxes ) {
		
		//print_r( $df_boxes );
		
			foreach( $df_boxes as $id => $df_box ) {
				$df_box_objects[$id] = new mph_datefield;
				$df_box_objects[$id]->define_box( $df_box );
			}
		} else {
			$df_boxes = array();
		}
		/* Add my meta boxes! */
		if( $df_box_objects )
			add_action( 'add_meta_boxes', 'df_meta_boxes');
	endif;

	/* 
	*
	*	Run through all defined boxes.
	*	Check if it applies to this post type.
	* 	If so - & create meta boxes. 
	*
	*/
	function df_meta_boxes( ) {
		global $post, $df_box_objects;
		foreach( $df_box_objects as $df_box ) {
			
			//Get current post type. If is a revision - get parent post type.
			$post_type = get_post_type( $post->ID );
			if( $post_type == 'revision' ) {
				global $post;
				$post_type = get_post_type( $post->post_parent );
			}		
			
			if( in_array( $post_type, $df_box->post_types ) ) :
				foreach( $df_box->post_types as $post_type ) {
					add_meta_box( $df_box->id . "_box", $df_box->title, 'df_meta_fields', $post_type, 'side', 'low', $df_box );
				}
			endif;
		}
	}

	/*
	*	A sort of Helper function. Call df_create_fields for each instance of the class. Called from df_meta_boxes() 
	*/
	function df_meta_fields( $post, $mph_var ) {
		$date_field_object = $mph_var['args'];
		$date_field_object->df_create_fields();
	}


/* Save my date field data */
add_action('save_post', 'df_save_fields');

function df_save_fields( $post_id ) {

	// Retrieve all boxes
	global $df_box_objects;
	
	// A few checks before we start
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ))
		return $post_id;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ))
		return $post_id;
	}
	
	//Get current post type. If is a revision - return.
	$post_type = get_post_type( $post_id );
	if( $post_type == 'revision' ) {
		return $port_id;
	}
	
	/**
	*	Run through each box. If it applies to this post type - proceed.
	* 	Set up long date array from post data for each fiel - convert to timestamp, and save/update.
	*
	*/
	foreach( $df_box_objects as $df_box ) {	
	
		if( in_array( $post_type, $df_box->post_types ) ) :
			
			//Verify nonces.
			if ( !wp_verify_nonce( $_POST[ $df_box->id . '_nonce' ], 'date_field' . MPH_DATEFIELD_VERSION )) {
				return $post_id;
			}
			
			//Set up a timestamp for each of the fields from this box.
			foreach ( $df_box->fields as $field ) {
				
				$insert = false;
				$df_longtime = false;
				
				if( $field['type'] == 'text' ) {
			
					$insert = $_POST[ $df_box->df_field_basename($field['name']) ]; 
				
				} elseif ( $field['type'] == 'date' ) {
								
					$day =  $_POST[ $df_box->df_field_basename($field['name']) . '_day'];
					$month = $_POST[ $df_box->df_field_basename($field['name']) . '_month'];
					$year = $_POST[ $df_box->df_field_basename($field['name']) . '_year'];
					$hour = $_POST[ $df_box->df_field_basename($field['name']) . '_hour'];
					if( !$hour ) $hour = '00';
					$minute = $_POST[ $df_box->df_field_basename($field['name']) . '_minute'];
					if( !$minute ) $minute = '00';
					
					if( $day != 0 && $month != 0 && $year != '' && is_numeric($year) ) {
						$df_longtime = array( 'day' => $day, 'month' => $month, 'year' => $year, 'hour' => $hour, 'minute' => $minute );
					}
					
					$df_box->df_update_timestamp( $field['name'], $df_longtime	);
					$insert = $df_box->timestamp[$field['name']];	
				
				}
				
				if ($insert) {
					if( get_post_meta( $post_id, '_' . $df_box->df_field_basename($field['name']) , true) == '') {
						add_post_meta($post_id, '_' . $df_box->df_field_basename($field['name']), $insert);
					} elseif ($timestamp != get_post_meta( $post_id, '_' . $df_box->df_field_basename($field['name']) , true)  ) {
						update_post_meta($post_id, '_' . $df_box->df_field_basename($field['name']), $insert);
					}
				} else {
					delete_post_meta($post_id, '_' . $df_box->df_field_basename($field['name']) );
				}
			}
			
		endif;
	}
}

?>