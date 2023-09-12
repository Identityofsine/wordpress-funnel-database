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
}
add_action('admin_menu', 'funnel_plugin_menu');

function funnel_plugin_settings_page()
{
}

function funnel_plugin_create_page()
{
	//create an input form that takes in a funnel_id, funnel_message, and funnel_active and calls the function wps_db_submit_funnel_element
	//create a form that takes in a funnel_id, funnel_message, funnel_active
	//create a button that calls the function wps_db_submit_funnel_element
	//create a table that displays all the funnel elements
	if (isset($_POST['submit_funnel'])) {
		$funnel_message = $_POST['funnel_message'];
		//convert post into a funnel object
		$funnel_obj = new FunnelObject(
			-1,
			$funnel_message,
			isset($_POST['active']),
			isset($_POST['phone']),
			$_POST['id-hero-image'],
			$_POST['id-header-icon'],
			$_POST['header_text'],
			$_POST['header_subtext'],
			$_POST['button_text']
		);
		$db_response = wps_db_submit_funnel_element($funnel_obj);
		if ($db_response->status === 'error') {
			echo $db_response->message;
			return;
		}
	}
?>
	<h1>Submit Funnel Element</h1>
	<form method="post" action="" class="flex column gap-1 media-page">
		<!-- Handle all data types in FunnelObject  -->
		<div class="content-container flex column gap-1">
			<h2>Funnel Properties</h2>
			<div class="flex align-center gap-05">
				<!-- message -->
				<label>Funnel Message:</label>
				<input type="text" name="funnel_message">
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
					<?php return_wordpress_media_files('placeholder-img header', 'hero-image') ?>
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
					<?php return_wordpress_media_files('placeholder-img icon', 'header-icon') ?>
				</div>
			</div>

			<div class="flex align-center gap-05">
				<!-- header text -->
				<label>Header Text</label>
				<input type="text" name="header_text">
			</div>

			<div class="flex align-center gap-05">
				<!-- header subtext -->
				<label>Header Subtext</label>
				<input type="text" name="header_subtext">
			</div>

			<div class="flex align-center gap-05">
				<!-- button text -->
				<label>Button Text</label>
				<input type="text" name="button_text">
			</div>
		</div>


		<?php submit_button("Create Funnel", "primary", "submit_funnel"); ?>
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
		$db_response = wps_db_set_funnel_active($funnel_id);
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
									<button class="button disabled">Already Active</button>
								<?php else : ?>
									<input type="hidden" name="funnel_id" value="<?php echo esc_attr($funnel->id); ?>">
									<button class="button" type="submit" name="submit_funnel_change">Activate</button>
								<?php endif; ?>
							</form>
						</td>
						<td>
							<button class="button" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=view-funnel-data-elements&funnel_id=' . $funnel->id)); ?>'">View Data</button>
						</td>
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
	$db_response = new DatabaseResponse('fail', 'No funnel id set');
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
