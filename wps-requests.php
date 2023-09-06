<?php

require_once('wps-database.php');
require_once('wps-response.php');


/**
 * {
 *  "funnel_id":int,
 *  "funnel_message":string,
 * 	"email":string | undefinied
 * 	"number":string | undefinied
 * }
 * 
 * 
 */

function wps_rest_handle_request($wp) {

	//check if number or email is set -- then call function responsible for database
	$response = new DatabaseResponse('fail', 'No email or number set');		

	//check if 'funnel_id' or 'funnel_message' are undefined, if so return false
	if (!isset($wp['funnel_id']) || !isset($wp['funnel_message'])) {
		wp_send_json(['status' => 'error', 'message' => 'No funnel id or message set']);
		exit();
	}

	try {
		//create a variable that holds the response
		if (isset($wp['number'])) {
			$response = wps_db_submit_phone_number($wp['funnel_id'], $wp['funnel_message'], $wp['number']);
		} else if (isset($wp['email'])) {
			$response = wps_db_submit_email($wp['funnel_id'], $wp['funnel_message'], $wp['email']);
		} else {
			throw new Exception('No number or email set');
		}

	} catch (Exception $e) {
		wp_send_json(['status' => 'error', 'message' => $e->getMessage()]);
		exit();
	}

	wp_send_json(['status' => $response->status, 'message' => $response->message]);
	exit();
}
