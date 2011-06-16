<?php	

add_action('admin_menu', 'df_admin_menu');

function df_admin_menu() {

  add_options_page('Date Field Settings', 'Date Fields', 'manage_options', 'df_datefield', 'df_admin');

}

function df_admin() {

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	switch( $_GET['action'] ) {
		case 'edit' :
			df_admin_edit();
			break;
		case 'save' :
			$errors = df_admin_save();
			df_admin_default( $errors );
			break;
		case 'delete' :
			df_admin_delete();
			df_admin_default();
			break;
		default: 
			df_admin_default();
			break;
	}

} //end df_admin()


function df_admin_default( $errors = false ) {

global $df_boxes;

	?>
  
<style type="text/css">
	#icon-options-datefield { background: url("../wp-content/plugins/date_field.1.0/icon_admin.png") no-repeat center center;}
	#df_admin label, #df_admin input[type="text"] {margin-right:10px;}
	#df_admin h4 {margin:0;}
	#df_admin fieldset { margin-bottom:10px; }
	#df_admin .labelfieldset { display:inline-block; width:100px; }
	#df_new_post_types {padding:10px;}
	#df_admin fieldset { padding:0; }
	.df_new_field_type label,
	#df_new_post_types label 	{ display: inline-block; width: auto; margin-right: 0px; text-transform:capitalize; }
	.df_new_field_type input,
	#df_new_post_types input	{ display: inline-block; margin-right: 15px; width: auto; }
</style>

<div id="df_admin" class="wrap">
<div class="icon32" id="icon-options-datefield"><br/></div>
<h2>Date Fields</h2>

<?php
if( $errors ) {
	foreach( $errors as $error ) {
		echo '<p class="error">' . $error . '</p>';
	}
}
?>

<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
		<form method="get" action="" id="posts-filter">
			<table cellspacing="0" class="widefat fixed">
				<thead>
				<tr>
				<!--<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>-->
				<th style="" class="manage-column column-name" id="df_title" scope="col">Title</th>
				<th style="" class="manage-column column-description" id="df_description" scope="col">Description</th>
				<th style="" class="manage-column column-fields num" scope="col">Fields</th>
				<th style="" class="manage-column column-post-types" scope="col">Post Types</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
				<!--<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>-->
				<th style="" class="manage-column column-name" id="name" scope="col">Name</th>
				<th style="" class="manage-column column-description" id="description" scope="col">Description</th>
				<th style="" class="manage-column column-fields num" scope="col">Fields</th>
				<th style="" class="manage-column column-post-types" scope="col">Post Types</th>
				</tr>
				</tfoot>
				<tbody>
				<?php //print_r($df_boxes); ?>
				<?php //echo $df_boxes; ?>
				<?php 
					if( $df_boxes ) {
						foreach ( $df_boxes as $id => $df_box ) { 
				?>
					<tr class="iedit alternate" id="link-cat-2">
						<!--<th class="check-column" scope="row">&nbsp;</th>-->
						<td class="name column-name">
							<a title="Edit &quot;<?php echo $df_box['title']; ?>&quot;" href="options-general.php?page=df_datefield&action=edit&id=<?php echo $id; ?>" class="row-title"><?php echo $df_box['title']; ?></a><br/>
							<div class="row-actions">
								<span class="edit"><a href="options-general.php?page=df_datefield&action=edit&id=<?php echo $id; ?>">Edit</a> | </span>
								<span class="delete"><a onclick="if ( confirm('You are about to delete this date field \'<?php echo $df_box['title']; ?>\'\n  \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;" href="options-general.php?page=df_datefield&action=delete&id=<?php echo $df_box['id']?>" class="submitdelete">Delete</a></span>
							</div>
						</td>
						<td class="description column-description"><?php echo $df_box['description']; ?></td>
						<td class="fields column-fields num"><?php echo count( $df_box['fields'] ); ?></td>
						<td class="post-types column-post-types"><?php 
							$i = 0; 
							foreach( $df_box['post_types'] as $post_type) { 
								if( $i > 0 ){ echo ', '; } 
								echo $post_type; $i++;} 
						?></td>
					</tr>	
				
				<?php 
						} //end foreach
					}	//endif
				?>
				</tbody>
			</table>
		</form>
		</div>
	</div><!-- /col-right -->
	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<h3 class="form-field">Add New Date Field Box</h3>
				<form action="options-general.php?page=df_datefield&action=save" method="post" class="add:the-list: validate" id="addcat" name="addcat">
				<input type="hidden" value="new" name="df_id" />
				<input type="hidden" value="http://localhost/vanilla/wp-admin/link-manager.php" name="_wp_original_http_referer"/><input type="hidden" value="68ca357346" name="_wpnonce" id="_wpnonce"/><input type="hidden" value="/vanilla/wp-admin/edit-link-categories.php" name="_wp_http_referer" />
				<div class="form-field form-required">
					<label for="df_title">Date Field Box Name</label>
					<input type="text" aria-required="true" size="40" value="<?php if( $_POST['df_id'] == 'new') echo $_POST['df_title']; ?>" id="df_title" name="df_title"/>
				</div>
				<div class="form-field">
					<label>Date Fields</label>
						<?php $c = 0; if( $_POST['df_id'] == 'new' && isset($_POST['df_field_new']) ) : foreach($_POST['df_field_new'] as $df_new) { ?>
							<input type="text" size="40" value="<?php echo $df_new; ?>" name="df_field_new[<?php echo $c; ?>]">
						<?php $c++; } endif; ?>
					<input type="text" size="40" value="" id="df_fields" name="df_field_new[<?php echo $c; ?>][name]" />
						<label for="df_field_new[<?php echo $c; ?>][type]">Type</label>
						<select id="df_field_new[<?php echo $c; ?>][text]" name="df_field_new[<?php echo $c; ?>][type]">
						<option value="text" />Text</option>
						<option value="date" />Date</option>
						</select>
					<br/><a href=''>Add New</a>
				</div>
				<div class="form-field">
					<label for="df_description">Date Field Box Description</label>
					<textarea cols="40" rows="5" id="df_description" name="df_description"><?php if( $_POST['df_id'] == 'new' ) echo $_POST['df_description']; ?></textarea>
					<p>The description is not essential, but can be used to indicate what the date field box is used for. It will be displayed abover the date fields when editing posts.</p>
				</div>
				<fieldset id="df_new_post_types">
					<legend>Post Types</legend><br/>
					<?php
					$post_types=get_post_types(array('public'=>true), 'names' , 'and'); 
					// We don't want to enable for attachments do we?
					unset( $post_types[array_search ( 'attachment' , $post_types )]);
					foreach( $post_types as $post_type ) {
						if( $_POST['df_id'] == 'new' && isset($_POST['df_post_types']) && in_array( $post_type, $_POST['df_post_types'] ) ) { $checked = 'checked="checked"'; } else { $checked = false; }
						echo '<label for="df_post_type_'.$post_type.'">'.$post_type.'</label><input type="checkbox" ' . $checked . ' value="'.$post_type.'" name="df_post_types[]" id="df_post_type_'.$post_type.'"/>';
					} ?>
				</fieldset>
				<p class="submit"><input type="submit" value="Add Date Field" name="submit" class="button"></p>
				</form>
			</div>
		</div>
	</div><!-- /col-left -->
</div><!-- /col-container -->
</div>

<?php
	} //end df_admin_default()

function df_admin_edit() {
	global $df_boxes, $df_box_objects;
	$df_box2 = $df_boxes[$_GET[id] ];
	$df_box = $df_box_objects[$_GET['id']];
	?>
<div id="df_admin_edit" class="wrap">

	<style type="text/css">
		#df_edit_fields input { margin-bottom:10px; margin-right:10px;}
		#df_edit_post_types label { margin-right:15px; text-transform:capitalize;}
		#df_edit_post_types input { margin-right:5px;}
		.df_form-field h4 {margin-top:0;}
		.df_form-field p {margin:0 0 1em;}
	</style>

	<h2>Edit Date Field Box</h2>

	<form class="validate" action="options-general.php?page=df_datefield&amp;action=save" method="post" id="df_edit" name="df_edit">
		<input type="hidden" value="<?php echo $df_box->id; ?>" name="df_id" />
		<input type="hidden" value="http://localhost/vanilla/wp-admin/edit-link-categories.php" name="_wp_original_http_referer" /><input type="hidden" value="f3826282cc" name="_wpnonce" id="_wpnonce" /><input type="hidden" value="/vanilla/wp-admin/link-category.php?action=edit&amp;cat_ID=2" name="_wp_http_referer" />	
		<table class="form-table">
			<tbody>
				<tr class="df_form-field form-required">
					<th valign="top" scope="row"><label for="df_title">Date Field Box Title</></th>
					<td><input type="text" aria-required="true" size="40" value="<?php echo $df_box->title; ?>" id="df_title" name="df_title"/></td>
				</tr>
				<tr id="df_edit_fields" class="df_form-field">
					<th valign="top" scope="row"><label>Fields</label></th>
					<td>
						<?php 
						if( is_array( $df_box->fields ) ) { 
						foreach( $df_box->fields as $id => $field ) { if( !empty($field) ) {; ?>
							<input type="text" size="40" value="<?php echo $field[name]; ?>" id="df_field_<?php echo $df_box->df_field_nicename($field[name]); ?>" name="df_fields[<?php echo $id; ?>][name]"/>
							<select name="df_fields[<?php echo $id; ?>][type]">
								<option value="text" <?php if( $field[type] == 'text' ) { echo 'selected="selected"'; } ?>>Text</option>
								<option value="date" <?php if( $field[type] == 'date' ) { echo 'selected="selected"'; } ?>>Date</option>
							</select>
							<br/>
						<?php } } } ?>
						<label>Add New Field</label><br/>
						<input type="text" size="40" value="" id="df_field_new" name="df_field_new[<?php $id++; echo $id; ?>][name]"/>
						<select id="df_field_new" name="df_field_new[<?php echo $id; ?>][type]">
							<option value="text">Text</option>
							<option value="date">Date</option>
						</select>
					</td>
				</tr>
				<tr class="df_form-field">
					<th valign="top" scope="row"><label for="df_description">Description (optional)</label></th>
					<td><textarea style="width: 97%;" cols="50" rows="5" id="df_description" name="df_description"><?php echo $df_box->description; ?></textarea><br/>
					<span class="description">The description is not essential, but can be used to indicate what the date field box is used for. It will be displayed abover the date fields when editing posts.</span></td>
				</tr>
				<tr id="df_edit_post_types" class="df_form-field">
					<th valign="top" scope="row"><label>Post Types (required)</label></th>
					<td>
					<?php 
						$post_types=get_post_types(array('public'=>true), 'names' , 'and'); 
						
						// We don't want to enable for attachments do we?
						unset( $post_types[array_search ( 'attachment' , $post_types )]);
						
						foreach( $post_types as $post_type ) {
							if( in_array( $post_type, $df_box->post_types) ) {$checked='checked="checked"';} else { $checked = false;}
							echo '<input type="checkbox" ' . $checked . ' value="'.$post_type.'" name="df_post_types[]" id="df_post_type_'.$post_type.'"/><label for="df_post_type_'.$post_type.'">'.$post_type.'</label>';
						}
						
						?>
						<br/><span class="description">Select the post types for which you want this date field box to apply.</span>
					</td>
				</tr>
				<tr id="df_how-to-use" class="df_form-field">
					<th valign="top" scope="row"><h4>Using these date fields:</h4></th>
					<td>
						<?php global $df_box_objects; foreach( $df_box->fields as $field ) { ?>
						<p><strong><?php echo $field['name']; ?></strong>: meta key: <code>_<?php echo $df_box->id; ?>_<?php echo $df_box->df_field_nicename($field['name']); ?></code></p>
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
	<p class="submit"><input type="submit" value="Update Date Field Box" name="submit" class="button-primary" /></p>
	</form>
</div>

<?php	
} // end df_admin_edit

function df_admin_save() {
	
	global $df_boxes;
	
	//Get id, or generate a new one if neccessary.
	if( $_POST['df_id'] == 'new' ) {
		//Get next array id.
		$keys = array_keys( $df_boxes );
		foreach( $keys as $id => $key ) {
			$keys[$id] = intval( str_replace('df_', '', $key) );
		}
		if( !empty( $keys ) ) {
			$df_id = max( $keys );
		} else {
			$df_id = 0;
		}
		$df_id ++;
		$df_id = 'df_' . $df_id;
	} else {
		$df_id = $_POST['df_id'];
	}	
	
	//echo 'errors:';
	//print_r($_POST['df_field_new'] );
	
	$errors = false;
	if( $_POST['df_title'] == '' ) {	$errors['no_title'] = 'You must specify a title.'; }
	if( !is_array( $_POST['df_post_types']) ) { $errors['no_post_type'] = 'You must specify at least 1 post type.'; }
	if( isEmpty( $_POST['df_field_new']) ) { $errors['no_fields'] = 'You must specify at least 1 field.'; }
	if( $errors ) { return $errors; }		
	
	
	$df_box = array(
			title => $_POST['df_title'], 
			description => $_POST['df_description'],
			id => $df_id,
			post_types => $_POST['df_post_types'],
			fields => $_POST['df_fields']
	);
	
	//Add new fields.
	if( is_array($_POST['df_field_new']) ) :
		foreach( $_POST['df_field_new'] as $new_field ) {
			if( $new_field[name] != '' ) {
				$df_box['fields'][] = $new_field;
			}
		}
	endif;
	
	//delete empty.
	if( is_array($df_box['fields']) ) :
		foreach( $df_box['fields'] as $id => $field ) {
			if( $field['name'] == '' ) 
				unset( $df_box['fields' ][$id] );
		}		
	endif;
	
	$df_boxes_temp = array(
		$df_id => $df_box
	);
		
	$df_boxes = array_merge( $df_boxes, $df_boxes_temp );
	update_option('df_date_fields', $df_boxes);

} // end of df_admin_save()

function df_admin_delete() {
	
	global $df_boxes;
	
	unset( $df_boxes[ $_GET[ 'id' ] ] ) ;
	
	update_option('df_date_fields', $df_boxes);
	
}

?>