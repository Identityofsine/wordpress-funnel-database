<?php


//run function on install


//function that creates the table in the database on install
function wps_funnel_database_install()
{
	init_funnel_object_database();
	init_funnel_database();
}

function init_funnel_database()
{
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
		funnel_sent boolean NOT NULL DEFAULT FALSE,
		PRIMARY KEY (id),
		FOREIGN KEY (funnel_id) REFERENCES wp_funnel_object_database(id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

function init_funnel_object_database()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	$charset_collate = $wpdb->get_charset_collate();

	//make sure only one element in the table can have active set to true
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		funnel_message varchar(255) NOT NULL,
		active boolean NOT NULL DEFAULT FALSE,
		phone boolean NOT NULL DEFAULT TRUE,
		hero_image varchar(255),
		header_icon varchar(255), 
		header_text varchar(255),
		header_subtext varchar(255),
		button_text varchar(255),
		message BLOB DEFAULT '',
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}



function wps_funnel_database_uninstall()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}


//submit
function wps_db_submit_phone_number($funnel_id, $funnel_message, $phone_number): DatabaseResponse
{
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
			throw new Exception('Database error: ' . $wpdb->last_error);
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Phone number submitted');
}

//submit emails
function wps_db_submit_email($funnel_id, $funnel_message, $email)
{
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

/**
 * Write a function that searches for entries under a funnel_id
 */
function wps_db_get_data_by_funnel_id($funnel_id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	try {
		$funnel_data = $wpdb->get_results("SELECT * FROM $table_name WHERE funnel_id = $funnel_id");
		if ($funnel_data === null) {
			throw new Exception('No data for this funnel id');
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', $funnel_data);
}

function wps_db_get_data_all_funnel()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_database';
	try {
		$funnel_data = $wpdb->get_results("SELECT * FROM $table_name");
		if ($funnel_data === null) {
			throw new Exception('No data for this funnel id');
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', $funnel_data);
}

/**
 * {
 *   "funnel_message":string,
 * 	 "active":boolean
 * }
 */

// submit a new funnel_element object into funnel_element table
function wps_db_submit_funnel_element(FunnelObject $funnel_object): DatabaseResponse
{
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
				'funnel_message' => $funnel_object->message,
				'active' => $funnel_object->active,
				'phone' => $funnel_object->phone,
				'hero_image' => $funnel_object->hero_image,
				'header_icon' => $funnel_object->header_icon,
				'header_text' => $funnel_object->header_text,
				'header_subtext' => $funnel_object->header_subtext,
				'button_text' => $funnel_object->button_text,
				'message' => $funnel_object->message
			)
		);
		if ($db_response === false) {
			throw new Exception('Database error: ' . $wpdb->last_error);;
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Funnel element submitted');
}

function wps_db_drop_funnel_element($funnel_id): DatabaseResponse
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	try {
		$db_response = $wpdb->delete(
			$table_name,
			array(
				'id' => $funnel_id
			)
		);
		if ($db_response === false) {
			throw new Exception('Database error: ' . $wpdb->last_error);;
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Funnel element deleted');
}

function wps_db_disable_all_funnel_element()
{
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
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'All funnel elements disabled');
}

function wps_db_update_funnel_element(FunnelObject $funnel_obj): DatabaseResponse
{
	if ($funnel_obj->id === -1) {
		//if somehow the funnel_obj is a new funnel
		return wps_db_submit_funnel_element($funnel_obj);
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	try {
		$db_response = $wpdb->update(
			$table_name,
			array(
				'funnel_message' => $funnel_obj->message,
				'active' => $funnel_obj->active,
				'phone' => $funnel_obj->phone,
				'hero_image' => $funnel_obj->hero_image,
				'header_icon' => $funnel_obj->header_icon,
				'header_text' => $funnel_obj->header_text,
				'header_subtext' => $funnel_obj->header_subtext,
				'button_text' => $funnel_obj->button_text
			),
			array(
				'id' => $funnel_obj->id
			)
		);
		if ($db_response === false) {
			throw new Exception('Database error: ' . $wpdb->last_error);
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', 'Funnel element updated');
}

function wps_db_get_current_funnel(): DatabaseResponse
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	try {
		$found_funnel_element = $wpdb->get_row("SELECT * FROM $table_name WHERE active = true");
		if ($found_funnel_element === null) {
			throw new Exception('No active funnel element');
		}
		$found_funnel_element->active = (bool)$found_funnel_element->active;
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	//cast the result into a FunnelObject
	$funnel_element = new FunnelObject(
		$found_funnel_element->id,
		$found_funnel_element->funnel_message,
		(bool) $found_funnel_element->active,
		(bool) $found_funnel_element->phone,
		$found_funnel_element->hero_image,
		$found_funnel_element->header_icon,
		$found_funnel_element->header_text,
		$found_funnel_element->header_subtext,
		$found_funnel_element->button_text
	);

	return new DatabaseResponse('success', $funnel_element);
}

function wps_db_get_funnel_by_id($funnel_id): DatabaseResponse
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';

	try {
		$funnel_element = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $funnel_id");

		if ($funnel_element === null) {
			throw new Exception('No funnel element');
		}

		return new DatabaseResponse('success', $funnel_element);
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
}

function wps_db_get_funnels(): DatabaseResponse
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	try {
		$funnel_elements = $wpdb->get_results("SELECT * FROM $table_name");
		if ($funnel_elements === null) {
			throw new Exception('No funnel elements');
		}
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
	return new DatabaseResponse('success', $funnel_elements);
}

function wps_db_set_funnel_active($funnel_id): DatabaseResponse
{
	//check if the funnel element exists
	global $wpdb;
	$table_name = $wpdb->prefix . 'funnel_object_database';
	$does_funnel_exist = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $funnel_id");
	if ($does_funnel_exist === null) {
		return new DatabaseResponse('error', 'Funnel element does not exist');
	}
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

		$wpdb->update(
			$table_name,
			array(
				'active' => true
			),
			array(
				'id' => $funnel_id
			)
		);
		return new DatabaseResponse('success', 'Funnel element set to active');
	} catch (Exception $e) {
		return new DatabaseResponse('error', $e->getMessage());
	}
}
