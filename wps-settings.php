<?php

function funnel_plugin_menu() {
	add_menu_page(
		'Funnel Plugin Settings', // Page title
		'Funnel Plugin', // Menu title
		'manage_options', // Capability required to access
		'funnel-plugin-settings', // Menu slug
		'funnel_plugin_settings_page', // Callback function to display the page
		'dashicons-chart-line', // Icon (optional)
		30 // Position in the menu (optional)
	);
}

add_action('admin_menu', 'funnel_plugin_menu');
