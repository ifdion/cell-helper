<?php
/* ajax : custom function to detect wheter a request is made by ajax or not
---------------------------------------------------------------
*/

if (!function_exists('ajax_request')) {
	function ajax_request(){
		if(
			(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
			(isset($_REQUEST['api']))
		) {
			return true;
		} else {
			return false;
		}
	}
}

/* ajax : custom function to create an ajax response or http location redirect
---------------------------------------------------------------
*/

if (!function_exists('ajax_response')) {
	function ajax_response($data,$redirect = false){
		if(ajax_request()){
			$data_json = json_encode($data);
			echo $data_json;
			exit;
		} else {
			$_SESSION['global_message'][] = $data;
			if ($redirect) {
				wp_redirect( $redirect );
				exit;
			} else {
				echo 'error : missing redirect';
			}
		}
	}	
}

/* global message 
---------------------------------------------------------------
*/

if (!function_exists('setup_global_message')) {
	function setup_global_message(){
		global $global_message;
		if ( isset( $_SESSION['global_message'] ) ){
			$global_message = $_SESSION['global_message'];
			unset( $_SESSION['global_message'] );
		}
	}
}

if (!function_exists('the_global_message')) {
	function the_global_message($additional_class){
		global $global_message;
		// print_r($global_message);
		// wp_die('die' );
		if ($global_message != '' && (count($global_message) > 0)) {
			foreach ($global_message as $message){
				?>
					<div id="" class="alert alert-<?php echo $message['type'].' '.$message['type'].' '.$additional_class ?>">
						<button type="button" class="close" data-dismiss="alert">×</button> <span><?php echo $message['message'] ?></span>
					</div>
				<?php
			}
		}
		$global_message = false;
	}	
}

/* ajax : handle multiple file upload array
---------------------------------------------------------------
*/
if (!function_exists('rearrange')) {
	function rearrange( $arr ){
		foreach( $arr as $key => $all ){
			foreach( $all as $i => $val ){
				$new[$i][$key] = $val;
			}
		}
		return $new;
	}
}


/* ajax : insert uploaded file as attachment
---------------------------------------------------------------
*/
if (!function_exists('attach_uploads')) {
	function attach_uploads($uploads,$post_id = 0,$attachment_meta = 0){
		$files = rearrange($uploads);
		if($files[0]['name']==''){
			return false;	
		}
		foreach($files as $file){
			$upload_file = wp_handle_upload( $file, array('test_form' => false) );
			$attachment = array(
			'post_mime_type' => $upload_file['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload_file['file'])),
			'post_content' => '',
			'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
			$attach_array[] = $attach_id;
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			if (is_array($attachment_meta)) {
				foreach ($attachment_meta as $key => $value) {
					$post_meta = add_post_meta( $attach_id, $key, $value);
				}
			}
		}
		return $attach_array;
	}	
}

/* wp-admin : disable non administrator to access wp-admin
---------------------------------------------------------------
*/
if (!function_exists('administrator_only')) {
	function administrator_only(){
		if( !defined('DOING_AJAX') && !current_user_can('administrator') ){
			wp_redirect( home_url() );
			exit();
		}
	}
}

/* wp-admin : disable non administrator to access wp-admin
---------------------------------------------------------------
*/
if (!function_exists('disable_admin_bar')) {
	function disable_admin_bar(){
		// if (!current_user_can('administrator') && !is_admin()) {
			return false;
		// } else {
		// 	return true;
		// }
	}
}

/* helper : check if is descendant 
---------------------------------------------------------------
*/

if(!function_exists('is_descendant')){
	function is_descendant( $page, $ancestor = false ) {
		if( !is_object( $page ) ) {
			$page = intval( $page );
			$page = get_post( $page );
		}
		if( is_object( $page ) ) {
			if( isset( $page->ancestors ) && !empty( $page->ancestors ) ) {
				if( !$ancestor ){
					return true;
				}elseif ( in_array( $ancestor, $page->ancestors ) ){
					return true;
				}
			}
		}
		return false;
	}
}

/* helper : get id from slug 
---------------------------------------------------------------
*/
if (!function_exists('get_id_by_slug')) {
	function get_id_by_slug($post_slug,$post_type){
		global $wpdb;
		$post_id = $wpdb->get_var(
			"	SELECT ID
				FROM wp_posts
				WHERE post_name = '$post_slug'
				AND post_type ='$post_type'
				LIMIT 0,1
			");
		return $post_id;
	}
}

/* helper : is_or_descendant_tax
---------------------------------------------------------------
*/
if (!function_exists('is_or_descendant_tax')) {
	function is_or_descendant_tax( $terms,$taxonomy){
		if (is_tax($taxonomy, $terms)){
				return true;
		}
		foreach ( (array) $terms as $term ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $term, $taxonomy);
			if ( $descendants && is_tax($taxonomy, $descendants) )
				return true;
		}
		return false;
	}
}
