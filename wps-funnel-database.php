<?php
/*
* Plugin Name:       RESTful Funnel Database
* Description:       Creates a REST API for the funnel database, allows for POST requests to submit data into a database
* Version:           1.0
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

add_action('rest_api_init', 'register_endpoint_handler');

function register_endpoint_handler() {

}