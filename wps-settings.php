<?php

require_once('wps-html-prefabs.php');

function funnel_plugin_menu()
{
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

	//view data by each funnel element
	add_submenu_page(
		'funnel-plugin-settings', // Parent menu slug
		'View Funnel Data', // Page title
		'View Funnel Data', // Menu title
		'manage_options', // Capability required to access
		'view-funnel-data-elements', // Menu slug
		'funnel_plugin_data_page' // Callback function to display the page
	);

	twilio_settings_fields();
}

add_action('admin_init', 'funnel_plugin_init_settings');
add_action('admin_menu', 'funnel_plugin_menu');


function funnel_plugin_init_settings()
{
	// Register a new setting for "reading" page.
	register_setting(
		'twilio_settings_group',        // Option group
		'twilio_account_id',            // Option name
		'sanitize_twilio_account_id'    // Sanitization callback
	);
	// Register the settings for Twilio Auth Token
	register_setting(
		'twilio_settings_group',        // Option group
		'twilio_auth_token',            // Option name
		'sanitize_twilio_auth_token'    // Sanitization callback
	);
	// Register the settings for Twilio Phone Number
	register_setting(
		'twilio_settings_group',        // Option group
		'twilio_phone_number',            // Option name
		'sanitize_twilio_phone_number'    // Sanitization callback
	);
}


function twilio_settings_fields()
{
	add_settings_section('twilio_settings_section', 'Twilio Settings', null, 'twilio_settings_page');

	add_settings_field('twilio_account_id', 'Twilio Account ID', 'funnel_plugin_setting_account_id', 'twilio_settings_page', 'twilio_settings_section');
	add_settings_field('twilio_auth_token', 'Twilio Auth Token', 'funnel_plugin_setting_auth_token', 'twilio_settings_page', 'twilio_settings_section');
	add_settings_field('twilio_phone_number', 'Twilio Phone Number', 'funnel_plugin_setting_phone_number', 'twilio_settings_page', 'twilio_settings_section');
}

add_action('admin_menu', 'twilio_settings_fields');

function funnel_plugin_settings_page()
{
?>
	<h1>Funnel Plugin Settings</h1>
	<form method="post" action="options.php">
		<?php
		settings_fields('twilio_settings_group');
		do_settings_sections('twilio_settings_page');
		submit_button();
		?>
	</form>
<?php
}

function funnel_plugin_setting_account_id()
{
	$twilio_account_id = get_option('twilio_account_id');
?>
	<div class="flex column gap-1">
		<!-- header icon -->
		<div class="flex align-bottom gap-1">
			<input type="text" name="twilio_account_id" value="<?php echo $twilio_account_id ?>">
		</div>
	<?php
}

function funnel_plugin_setting_auth_token()
{
	$twillo_auth_token = get_option('twilio_auth_token', false);
	?>
		<div class="flex column gap-1">
			<!-- header icon -->
			<div class="flex align-bottom gap-1">
				<input type="text" name="twilio_auth_token" value="<?php echo $twillo_auth_token ?>">
			</div>
		</div>
	<?php
}

function funnel_plugin_setting_phone_number()
{
	$twillo_phone_number = get_option('twilio_phone_number', false);
	?>
		<div class="flex column gap-1">
			<!-- header icon -->
			<div class="flex align-bottom gap-1">
				<input type="text" name="twilio_phone_number" value="<?php echo $twillo_phone_number ?>">
			</div>
		</div>
	<?php
}


function funnel_plugin_create_page()
{
	//create an input form that takes in a funnel_id, funnel_message, and funnel_active and calls the function wps_db_submit_funnel_element

	// check if post request (submittion or saving)
	if (isset($_POST['submit_funnel'])) {
		$funnel_message = $_POST['funnel_message'];
		//convert post into a funnel object

		//check if funnel_id is set, if so then set it to $funnel_id if not set it to -1
		$funnel_id = $_POST['funnel_id'] ?? -1;
		$funnel_obj = new FunnelObject(
			$funnel_id,
			$funnel_message,
			isset($_POST['active']),
			isset($_POST['phone']),
			$_POST['id-hero-image'],
			$_POST['id-header-icon'],
			$_POST['header_text'],
			$_POST['header_subtext'],
			$_POST['button_text']
		);
		//submit also updates
		$db_response = new DatabaseResponse('error', 'No funnel id set');

		//check $funnel_id as it if were a string, not int
		if ($funnel_id === '-1') {
			$db_response = wps_db_submit_funnel_element($funnel_obj);
			//redirect to view-funnel-data-elements
			if ($db_response->status === 'success') {
				echo '<script>window.location.href="' . esc_url(admin_url('admin.php?page=manage-funnel-data-elements')) . '";</script>';
			}
		} else {
			$db_response = wps_db_update_funnel_element($funnel_obj);
		}
		if ($db_response->status === 'error') {
			echo $db_response->message;
			return;
		}
	}
	//check if get request (editing)	
	else if (isset($_GET['funnel_id'])) {
		$funnel_id = $_GET['funnel_id'];
		$db_response = wps_db_get_funnel_by_id($funnel_id);
		if ($db_response->status === 'error') {
			echo $db_response->message;
			return;
		}
		//cast $db_response->message to FunnelObject
		$funnel_obj = new FunnelObject(
			$db_response->message->id,
			$db_response->message->funnel_message,
			$db_response->message->active,
			$db_response->message->phone,
			$db_response->message->hero_image,
			$db_response->message->header_icon,
			$db_response->message->header_text,
			$db_response->message->header_subtext,
			$db_response->message->button_text
		);
		//print out the funnel_obj in js
		echo '<script>console.log(' . json_encode($funnel_obj) . ');</script>';
	} else {
		$funnel_obj = new FunnelObject(-1, '', false, false, -1, -1, '', '', '');
	}
	?> <h1>Submit Funnel Element</h1>

		<?php
		if ($funnel_obj->id === -1) {
			echo '<span>Creating New Funnel</span>';
		} else {
			echo '<span>Editing Funnel : ' . $funnel_obj->message . '</span>';
		}
		?>

		<form method="post" action="" class="flex column gap-1 media-page">

			<!-- Handle ID of funnel for POST request -->
			<input type="hidden" name="funnel_id" value="<?php echo $funnel_obj->id ?>">

			<!-- Handle all data types in FunnelObject  -->
			<div class="content-container flex column gap-1">
				<h2>Funnel Properties</h2>
				<div class="flex align-center gap-05">
					<!-- message -->
					<label>Funnel Message:</label>
					<input type="text" name="funnel_message" placeholder="Funnel Message(ID)" value="<?php echo $funnel_obj->message ?>">
				</div>
				<div class="flex align-center gap-05">
					<!-- active boolean -->
					<label>Set Active?:</label>
					<input type="checkbox" name="active" checked>
				</div>
				<div class="flex align-center gap-05">
					<!-- phone number or email -->
					<label>Phone Number Funnel?:</label>
					<input type="checkbox" name="phone" checked>
				</div>


			</div>
			<div class="content-container flex column gap-1">
				<h2>Funnel Hero-Image</h2>
				<div class="flex column gap-1">
					<!-- hero image -->
					<!-- convert into wordpress api to import media -->
					<label>Hero Image</label>
					<div class="flex align-bottom gap-1">
						<?php return_wordpress_media_files('placeholder-img header', 'hero-image', $funnel_obj->hero_image) ?>
					</div>
				</div>
			</div>


			<!-- funnel-content-container -->
			<div class="content-container flex column gap-1">
				<h2>Funnel Display-Content</h2>
				<div class="flex column gap-1">
					<!-- header icon -->
					<!-- convert into wordpress api to import media -->
					<label>Header Icon</label>
					<div class="flex align-bottom gap-1">
						<?php return_wordpress_media_files(
							'placeholder-img icon',
							'header-icon',
							$funnel_obj->header_icon
						) ?>
					</div>
				</div>

				<div class="flex align-center gap-05">
					<!-- header text -->
					<label>Header Text</label>
					<input type="text" name="header_text" value="<?php echo '' . $funnel_obj->header_text ?>">
				</div>

				<div class="flex align-center gap-05">
					<!-- header subtext -->
					<label>Header Subtext</label>
					<input type="text" name="header_subtext" value="<?php echo '' . $funnel_obj->header_subtext ?>">
				</div>

				<div class="flex align-center gap-05">
					<!-- button text -->
					<label>Button Text</label>
					<input type="text" name="button_text" value="<?php echo $funnel_obj->button_text ?>">
				</div>
			</div>


			<?php submit_button($funnel_obj->id === -1 ? "Create Funnel" : "Save Funnel", "primary", "submit_funnel"); ?>
		</form>
	<?php
}



function funnel_plugin_manage_page()
{
	$db_response = wps_db_get_funnels();
	if ($db_response->status === 'error') {
		echo $db_response->message;
		return;
	}
	if (isset($_POST['submit_funnel_change'])) {
		$funnel_id = $_POST['funnel_id'];
		if ($funnel_id === '-1') {
			$db_response = wps_db_disable_all_funnel_element();
		} else
			$db_response = wps_db_set_funnel_active($funnel_id);
		if ($db_response->status === 'error') {
			echo $db_response->message;
			return;
		}
	} else if (isset($_POST['delete_funnel'])) {
		$funnel_id = $_POST['funnel_id'];
		$db_response = wps_db_drop_funnel_element($funnel_id);
		if ($db_response->status === 'error') {
			echo $db_response->message;
			return;
		}
	}
	$db_response = (array)wps_db_get_funnels()->message;
	?>
		<div class="wrap">
			<h2>Current Funnels</h2>
			<table class="wp-list-table widefat fixed" style="margin-top:1%; border:2px solid #f2f2f2;">
				<thead>
					<tr>
						<th>Funnel Id</th>
						<th>Funnel Message</th>
						<th>Activate</th>
						<th>View Details</th>
						<th>Edit</th>
						<th>Delete</th>
						<!-- Add more column headers as needed -->
					</tr>
				</thead>
				<tbody>
					<?php
					//write for loop using $db_response, treat it as an array of {active:boolean, funnel_message:string}
					foreach ($db_response as $funnel) : ?>
						<tr style="box-sizing: border-box;">
							<td><?php echo esc_html($funnel->id); ?></td>
							<td><?php echo esc_html($funnel->funnel_message); ?></td>
							<!-- Add more data columns as needed -->
							<td>
								<form method="post" action="">
									<?php if ($funnel->active) : ?>
										<input type="hidden" name="funnel_id" value="-1">
										<button class="button primary" type="submit" name="submit_funnel_change">Deactivate</button>
									<?php else : ?>
										<input type="hidden" name="funnel_id" value="<?php echo esc_attr($funnel->id); ?>">
										<button class="button" type="submit" name="submit_funnel_change">Activate</button>
									<?php endif; ?>
								</form>
							</td>
							<td>
								<button class="button" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=view-funnel-data-elements&funnel_id=' . $funnel->id)); ?>'">View Data</button>
							</td>
							<td>
								<button class="button" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=create-funnel-element&funnel_id=' . $funnel->id)); ?>'">Edit</button>
							</td>
							<td>
								<form method="post" action="">
									<input type="hidden" name="funnel_id" value="<?php echo esc_attr($funnel->id); ?>">
									<button class="button primary red" type="submit" name="delete_funnel" style="background-color: #fe6d73; color:white;">Delete</button>
								</form>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div style="width:fit-content; margin-left:auto;">
				<a href="admin.php?page=view-funnel-data-elements&funnel_id=-1">See All</a>
			</div>
		</div>
	<?php
}

function funnel_plugin_data_page()
{
	$db_response = new DatabaseResponse('error', 'No funnel id set');
	$is_all = false;
	$funnel_name = "_";
	if (!isset($_GET['funnel_id'])) {
		$db_response = wps_db_get_data_all_funnel();
		$is_all = true;
	} else {
		$funnel_id = $_GET['funnel_id'];
		if ($funnel_id === '-1') {
			$db_response = wps_db_get_data_all_funnel();
			$is_all = true;
		} else {
			$db_response = wps_db_get_data_by_funnel_id($funnel_id);
			$db_funnel_name_response = wps_db_get_funnel_by_id($funnel_id);
			//ignore error
			if (isset($db_funnel_name_response->message)) {
				$funnel_name = $db_funnel_name_response->message->funnel_message;
			}
		}
	}
	if ($db_response->status === 'error') {
		echo $db_response->message;
		return;
	}
	$funnel_data = (array)$db_response->message;

	?>
		<div class="wrap">
			<h2><?php echo $is_all ? 'Viewing Data Collected From All Funnels' : 'Viewing Data Collected From \'' . $funnel_name . '\'' ?></h2>
			<table class="wp-list-table widefat fixed" style="margin-top:1%; border:2px solid #f2f2f2;">
				<thead>
					<tr>
						<th>Funnel Id</th>
						<th>Funnel Message</th>
						<th>Funnel Email</th>
						<th>Funnel Phone Number</th>
						<!-- Add more column headers as needed -->
					</tr>
				</thead>
				<tbody>
					<?php
					//write for loop using $db_response, treat it as an array of {active:boolean, funnel_message:string}
					foreach ($funnel_data as $funnel) :
					?>
						<tr style="box-sizing: border-box;">
							<td><?php echo esc_html($funnel->funnel_id); ?></td>
							<td><?php echo esc_html($funnel->funnel_message); ?></td>
							<td><?php echo empty($funnel->funnel_email) ? '<b>N\A</b>' : $funnel->funnel_email; ?></td>
							<td><?php echo empty($funnel->funnel_phone) ? '<b>N\A</b>' : $funnel->funnel_phone; ?></td>
							<!-- Add more data columns as needed -->
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php
}
