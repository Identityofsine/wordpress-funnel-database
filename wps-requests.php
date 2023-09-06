<?php


//create a response type that allows my functions to have a same return type
class DatabaseResponse {
	public $status;
	public $message;


	//constructor that takes in a status and a message
	public function __construct($status = '', $message = '') {
		$this->status = $status;
		$this->message = $message;
	}
}

function wps_rest_handle_request($wp) {

	//check if number or email is set -- then call function responsible for database
	try {
		//create a variable that holds the response
		$response = new DatabaseResponse('success', 'success');		
		if (isset($wp['number'])) {
			wps_db_submit_phone_number($wp['funnel_id'], $wp['funnel_message'], $wp['number']);
		} else if (isset($wp['email'])) {
			wps_db_submit_email($wp['funnel_id'], $wp['funnel_message'], $wp['email']);
		} else {
			throw new Exception('No number or email set');
		}

	} catch (Exception $e) {
		wp_send_json(['status' => 'error', 'message' => $e->getMessage()]);
		exit();
	}

	wp_send_json(['status' => 'success']);
	exit();
}

