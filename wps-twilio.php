<?php

function send_twilio_message($recipient, $message)
{
	//twilio settings
	$phone_number = get_option('twilio_phone_number');
	$account_sid = get_option('twilio_account_id');
	$auth_token = get_option('twilio_auth_token');

	//twilio endpoint
	$url = 'https://api.twilio.com/2010-04-01/Accounts/' . $account_sid . '/Messages.json';

	//body setup
	$body = array(
		'Body' => $message,
		'To' => $recipient,
		'From' => $phone_number
	);

	//curl setup
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
	curl_setopt($ch, CURLOPT_USERPWD, $account_sid . ':' . $auth_token);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//send message
	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		//error
		$response = curl_error($ch);
	} else {
		//success
		$response = json_decode($response, true);
	}

	//close curl
	curl_close($ch);

	//return response
	return $response;
}
