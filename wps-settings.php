<?php

function funnel_plugin_menu() {
	add_menu_page(
		'Funnel Plugin Settings', // Page title
		'Funnel', // Menu title
		'manage_options', // Capability required to access
		'funnel-plugin-settings', // Menu slug
		'funnel_plugin_settings_page', // Callback function to display the page
		'dashicons-chart-line', // Icon (optional)
		100 // Position in the menu (optional)
	);
	// Add submenus
	//create funnel
	add_submenu_page(
		'funnel-plugin-settings', // Parent menu slug
		'Create Funnel Element', // Page title
		'Create Element', // Menu title
		'manage_options', // Capability required to access
		'create-funnel-element', // Menu slug
		'funnel_plugin_create_page' // Callback function to display the page
	);
	//view funnel elements
	add_submenu_page(
			'funnel-plugin-settings', // Parent menu slug
			'Manage and View Funnel Elements', // Page title
			'Manage and View Elements', // Menu title
			'manage_options', // Capability required to access
			'manage-funnel-elements', // Menu slug
			'funnel_plugin_manage_page' // Callback function to display the page
	);

}
add_action('admin_menu', 'funnel_plugin_menu');

function funnel_plugin_settings_page() {
}

function funnel_plugin_create_page () {

}

function funnel_plugin_manage_page () {

}