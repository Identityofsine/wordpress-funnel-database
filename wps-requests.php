<?php

require_once('wps-database.php');
require_once('wps-response.php');


/**
 * {
 *  "funnel_id":int,
 *  "funnel_message":string,
 * 	"email":string | undefinied
 * 	"phonenumber":string | undefinied
 * }
 * 
 * 
 */

function wps_rest_handle_request($wp)
{

	//check if number or email is set -- then call function responsible for database
	$response = new DatabaseResponse('fail', 'No email or number set');

	//check if 'funnel_id' or 'funnel_message' are undefined, if so return false
	if (!isset($wp['funnel_id']) || !isset($wp['funnel_message'])) {
		wp_send_json(['status' => 'error', 'message' => 'No funnel id or message set']);
		exit();
	}

	try {
		//create a variable that holds the response
		if (isset($wp['phonenumber'])) {
			$response = wps_db_submit_phone_number($wp['funnel_id'], $wp['funnel_message'], $wp['phonenumber']);
		}
		if (isset($wp['email'])) {
			$response = wps_db_submit_email($wp['funnel_id'], $wp['funnel_message'], $wp['email']);
		}
		if (!isset($wp['phonenumber']) && !isset($wp['email'])) {
			throw new Exception('No number or email set');
		}
	} catch (Exception $e) {
		wp_send_json(['status' => 'error', 'message' => $e->getMessage()]);
		exit();
	}

	wp_send_json(['status' => $response->status, 'message' => $response->message]);
	exit();
}

function wps_get_image_path($image_id)
{
	//get request, grab the current funnel element by DB_Command
	$image_src = wp_get_attachment_image_src($image_id, 'large');
	return $image_src;
}

function wps_rest_get_current_funnel_element($wp)
{
	//get request, grab the current funnel element by DB_Command
	$db_response = wps_db_get_current_funnel();

	//check for errors in response, if so return
	if ($db_response->status === 'error') {
		wp_send_json(['status' => $db_response->status, 'data' => $db_response->message]);
		exit();
	}

	//replace data->hero_image and data->header_icon with image paths
	$db_response->message->hero_image = wps_get_image_path($db_response->message->hero_image);
	$db_response->message->header_icon = wps_get_image_path($db_response->message->header_icon);

	//fix text and remove backslashes
	$db_response->message->header_text = FunnelObject::clear_backslashes($db_response->message->header_text);
	$db_response->message->header_subtext = FunnelObject::clear_backslashes($db_response->message->header_subtext);
	$db_response->message->button_text = FunnelObject::clear_backslashes($db_response->message->button_text);


	wp_send_json(['status' => $db_response->status, 'data' => $db_response->message]);
	exit();
}
