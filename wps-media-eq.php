<?php

add_action('admin_enqueue_scripts', 'load_wp_media_files');
function load_wp_media_files($page)
{
	// change to the $page where you want to enqueue the script
	if ($page == 'funnel_page_create-funnel-element') {
		// Enqueue WordPress media scripts
		wp_enqueue_media();
		// Enqueue custom script that will interact with wp.media
		wp_enqueue_script('wps_script', plugins_url('/js/media-script.js', __FILE__), array('jquery'), '0.1');
		// Enqueue CSS 
		wp_enqueue_style('wps_style', plugins_url('/css/style.css', __FILE__), array(), '0.1');
	}
}
// Path: js/media-script.js

// Ajax action to refresh the user image
add_action('wp_ajax_wps_get_image', 'wps_get_image');
function wps_get_image()
{
	if (isset($_GET['id']) && isset($_GET['img_id'])) {
		$img_id = $_GET['img_id'];
		$image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => $img_id));
		$data = array(
			'image'    => $image,
		);
		wp_send_json_success($data);
	} else {
		wp_send_json_error();
	}
}
