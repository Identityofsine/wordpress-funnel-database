<?php


//function that removes the table from the database on uninstall

function wps_funnel_database_install() {
	init_funnel_database();
	init_funnel_object_database();
}

//function that creates the table in the database on install
function init_funnel_database() {
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

function init_funnel_object_database() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	$charset_collate = $wpdb->get_charset_collate();

	//make sure only one element in the table can have active set to true
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		funnel_id mediumint(9) NOT NULL,
		funnel_message varchar(255) NOT NULL,
		active boolean NOT NULL DEFAULT FALSE,
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
function wps_db_submit_phone_number($funnel_id, $funnel_message, $phone_number) : DatabaseResponse {

	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	try {
		$db_response = $wpdb->insert(
			$table_name,
			array(
				'funnel_id' => $funnel_id,
				'funnel_message' => $funnel_message,
				'funnel_phone' => $phone_number
			)
		);
		//throw error if DB error happens
		if ($db_response === false) {
			throw new Exception('Database error');
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Phone number submitted');
}

//submit emails
function wps_db_submit_email($funnel_id, $funnel_message, $email) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	try {
		$wpdb->insert(
			$table_name,
			array(
				'funnel_id' => $funnel_id,
				'funnel_message' => $funnel_message,
				'funnel_email' => $email
			)
		);
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Email submitted');
}

// submit a new funnel_element object into funnel_element table
function wps_db_submit_funnel_element($funnel_obj) : DatabaseResponse { 
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	try {
		//change elements to false
		$wpdb->update(
			$table_name,
			array(
				'active' => false
			),
			array(
				'active' => true
			)
		);

		$db_response = $wpdb->insert(
			$table_name,
			array(
				'funnel_id' => $funnel_obj->funnel_id,
				'funnel_message' => $funnel_obj->funnel_message,
				'active' => $funnel_obj->active
			)
		);
		if($db_response === false) {
			throw new Exception('Database error');
		}
		


	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Funnel element submitted');
}