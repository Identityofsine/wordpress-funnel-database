<?php
/*
* Plugin Name:       RESTful Funnel Database
* Description:       Creates a REST API for the funnel database, allows for POST requests to submit data into a database
* Version:           1.2
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Kevin Erdogan
* Author URI:        https://identityofsine.github.io/
* License:           GPL v2 or later
* Text Domain:       stripe-client-secret
* Domain Path:       /ih-api
*/

/**
* add_action is a function that adds a callback function to an action hook. Actions are the hoks that the wordpress core launched at specific points during execution, or when specific events occur. 
*/

//change this to only allow local server
header("Access-Control-Allow-Origin: *");

//install hook inside
require_once('wps-database.php');
require_once('wps-requests.php');
require_once('wps-settings.php');

add_action('rest_api_init', 'register_endpoint_handler_funnel');

//run function on install

register_activation_hook(__FILE__, 'wps_funnel_database_install');
//hook that runs wps_funnel_database_uninstall
register_uninstall_hook(__FILE__, 'wps_funnel_database_uninstall');

function register_endpoint_handler_funnel() {
	//add two POST requests: 'submitNumber', 'submitEmail'
	add_action( 'rest_pre_serve_request', function () {
		header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Wpml-Language', true );
		header("Access-Control-Allow-Origin: *");
	});
	register_rest_route( 'funnel', '/submit', array(
		'methods' => 'POST',
		'callback' => 'wps_rest_handle_request',
	) );
	register_rest_route( 'funnel', '/current', array(
		'methods' => 'GET',
		'callback' => 'wps_rest_get_current_funnel_element',
	));
}

