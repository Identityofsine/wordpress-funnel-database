<?php

//run function on install

register_activation_hook(__FILE__, 'wps_funnel_database_install');
//hook that runs wps_funnel_database_uninstall
register_uninstall_hook(__FILE__, 'wps_funnel_database_uninstall');

//function that removes the table from the database on uninstall

function wps_funnel_database_install() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	$charset_collate = $wpdb->get_charset_collate();

	//create a sql query that creates a table (if it doesn't exist) that contains the following columns:
	//id, funnel_id (will eventaully do something with this but cant be null), funnel_message (one worded), funnel_email(can be null), funnel_phone(can be null), funnel_date (timestamp)
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		funnel_id mediumint(9) NOT NULL,
		funnel_message varchar(255) NOT NULL,
		funnel_email varchar(255) ,
		funnel_phone varchar(255) ,
		funnel_date timestamp DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}


function wps_funnel_database_uninstall() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}


//submi
function wps_db_submit_phone_number($funnel_id, $funnel_message, $phone_number) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	$wpdb->insert(
		$table_name,
		array(
			'funnel_id' => $funnel_id,
			'funnel_message' => $funnel_message,
			'funnel_phone' => $phone_number
		)
	);
}

