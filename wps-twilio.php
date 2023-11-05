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
		'From' => get_option('twilio_phone_number')
	);

	//curl setup
	$ch = curl_init($url);
}
