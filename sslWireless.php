<?php 
/**
*  Plugin Name: SSL Wireless SMS Notification
*  Plugin URI: https://sslwireless.com/
*  Description: This plugin allows to send login/register OTP and transactional alert for WooCommerce orders.
*  Version: 3.2.0
*  Stable tag: 3.2.0
*  WC tested up to: 9.3.3
*  Author: SSL Wireless
*  Author URI: sslwireless.com
*  Author Email: reza.farukh@sslwireless.com
*  License: GNU General Public License v3.0
*  License URI: http://www.gnu.org/licenses/gpl-3.0.html
**/
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SSLWireless_Woocommerce
 * @author     SSL Wireless <reza.farukh@sslwireless.com>
 */

	if (!defined('ABSPATH')) exit; // Exit if accessed directly

	define( 'SSLW_SMS_PATH', plugin_dir_path( __FILE__ ) );
	define( 'SSLW_SMS_URL', plugin_dir_url( __FILE__ ) );

	define ( 'SSLW_SMS_NOTIFICATION_VERSION', '3.2.0');
	
	global $plugin_slug;
	$plugin_slug = 'sslcare';
	$options = get_option( 'sslcare_notification' );

	// echo "<pre>";
	// print_r($options);

	global $api_hash_token;
	global $api_url;
	global $api_username;
	global $api_password;
	global $sslcare_api_sid;
	global $sslcare_otp_login_register_template;

	$api_hash_token = $options['api_hash_token'] ?? "";
	if(isset($options['api_url']) && !empty($options['api_url'])){
		$api_url = $options['api_url'];
	}else{
		$api_url = "https://smsplus.sslwireless.com/api/v3/send-sms";
	}
	$api_username = $options['api_username'] ?? "";
	$api_password = $options['api_password'] ?? "";
	$sslcare_api_sid = $options['sslcare_api_sid'] ?? "";
	if(isset($options['sslcare_otp_login_register_template']) && !empty($options['sslcare_otp_login_register_template'])){
		$sslcare_otp_login_register_template = $options['sslcare_otp_login_register_template'];
	}else{
		$sslcare_otp_login_register_template = "Your OTP is {{otp}}.\nThank You\n".get_bloginfo('name');
	}

	require_once( SSLW_SMS_PATH . 'lib/sslcare-init.php' );
	require_once( SSLW_SMS_PATH . 'lib/sslcare-admin-setting.php' );
	require_once( SSLW_SMS_PATH . 'lib/sslcare-woo-alert.php' );

	use Sslcare\Admin\Init\Sslcare_Init;
	use Sslcare\Admin\Setting\Sslcare_Admin_Setting;
	use Sslcare\Sms\Woosms\Sslcare_Woo_Alert;

	new Sslcare_Admin_Setting;

	if(isset($options['enable_plugin']) && !empty($options['enable_plugin']))
	{
		new Sslcare_Woo_Alert;
	}

	/**
	 * Hook plugin activation
	*/
	register_activation_hook( __FILE__, 'WcSslwirelessActivator' );
	function WcSslwirelessActivator() {
		Sslcare_Init::install_sslcare();
		$sslcare_installed_version = get_option( "sslcare_plugin_version" );

		if ( $sslcare_installed_version == SSLW_SMS_NOTIFICATION_VERSION ) {
			return true;
		}
		update_option( 'sslcare_plugin_version', SSLW_SMS_NOTIFICATION_VERSION );
	}

	// Action hook to trigger table creation when page refreshes both in frontend and backend
	add_action('admin_init', 'create_tables_on_admin_init');
	add_action('wp', 'create_tables_on_wp');

	// Function to create tables
	function create_tables_on_admin_init() {
		// This function will be triggered on admin panel initialization
		create_additional_tables();
	}

	function create_tables_on_wp() {
		// This function will be triggered on front-end page load
		create_additional_tables();
	}

	function create_additional_tables() {
		global $wpdb;
	
		// Check if the tables already exist
		$sslcare_otp_table_name = $wpdb->prefix . "sslcare_otp";
        $sslcare_otp_login_register_settings_table_name = $wpdb->prefix . "sslcare_otp_login_register_settings";

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}
	
		if ($wpdb->get_var("SHOW TABLES LIKE '$sslcare_otp_table_name'") != $sslcare_otp_table_name) {
			// Table doesn't exist, create it
			$sql1 = "CREATE TABLE $sslcare_otp_table_name (
				id mediumint(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                phone varchar(20) NULL,
                otp int(255) NULL,
                cap_data varchar(100) NULL,
                quantity int(255),
                ip_address varchar(20) NULL,
                sending_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY id (id)
                ) $charset_collate;";
			$wpdb->query($sql1);
		}
	
		if ($wpdb->get_var("SHOW TABLES LIKE '$sslcare_otp_login_register_settings_table_name'") != $sslcare_otp_login_register_settings_table_name) {
			// Table doesn't exist, create it
			$sql2 = "CREATE TABLE $sslcare_otp_login_register_settings_table_name (
				id mediumint(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                enable_otp varchar(20) NULL,
                page_id bigint(20) NULL,
                delete_otp_data_after bigint(20) NULL,
                UNIQUE KEY id (id)
                ) $charset_collate;";
			$wpdb->query($sql2);
		}
	}

	/**
	 * Hook plugin deactivation
	 */
	register_deactivation_hook( __FILE__, 'WcSslwirelessDeactivator' );
	function WcSslwirelessDeactivator() { }

	add_action( 'before_woocommerce_init', function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );


	function sslwireless_care_settings_link($links)
	{
	    $pluginLinks = array(
            'settings' => '<a href="'. esc_url(admin_url( 'admin.php?page=sslcare-notification')) .'">Settings</a>',
            'docs'     => '<a href="https://www.sslwireless.com/enterprise-solutions/api-based-sms/" target="blank">Docs</a>',
            'support'  => '<a href="mailto:reza.farukh@sslwireless.com">Support</a>'
        );

	    $links = array_merge($links, $pluginLinks);

	    return $links;
	}

	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sslwireless_care_settings_link');


	/* Custom CSS of OTP Login Register file for Backend---*/
	function wpdocs_enqueue_custom_admin_style() {
		wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ).'lib/asset/css/style-backend-sslcare-otp-login-register.css', false, '1.0.2' );
		wp_enqueue_style( 'custom_wp_admin_css' );
	}
	add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

	 /* An Admin page with Menu for OTP Login Register */
	function otp_login_register_admin_menu() {
		// Add submenu
		add_submenu_page(
			'sslcare-notification',  // Parent menu slug (use the same slug as the top-level menu)
			'OTP Login Register',               // Page title
			'OTP Login Register',                    // Menu title
			'manage_options',             // Capability required to access the page
			'sslcare-otp-login-register',  // Menu slug
			'otp_login_register_page_content' // Callback function to display the content
		);
		// Add submenu
		add_submenu_page(
			'sslcare-notification',  // Parent menu slug (use the same slug as the top-level menu)
			'All OTP Data',               // Page title
			'All OTP Data',                    // Menu title
			'manage_options',             // Capability required to access the page
			'all-otp-data',  // Menu slug
			'all_otp_data_page_content' // Callback function to display the content
		);
		// Add submenu
		add_submenu_page(
			'sslcare-notification',  // Parent menu slug (use the same slug as the top-level menu)
			'CSV Upload Mobile Number',               // Page title
			'CSV Upload Mobile Number',                    // Menu title
			'manage_options',             // Capability required to access the page
			'csv-upload-mobile-number',  // Menu slug
			'all_csv_upload_mobile_number_page_content' // Callback function to display the content
		);
	}

	add_action( 'admin_menu', 'otp_login_register_admin_menu' );

	function otp_login_register_page_content(){ ?>
	    <div class="admin-custom-container">
			<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
				<h1 class="title"><?php esc_html_e( 'OTP Login Register Settings', 'my-plugin-textdomain' ); ?></h1>
			</div>
			<?php 
                
                global $wpdb;
				$table_otp_login_register_settings = $wpdb->prefix . "sslcare_otp_login_register_settings";
				$checkData = $wpdb->get_row( "SELECT * FROM $table_otp_login_register_settings WHERE id =1");
            
                if(isset($_POST['submit_setting'])){					

					if($checkData){
						$result = $wpdb->query($wpdb->prepare("UPDATE $table_otp_login_register_settings SET 
							enable_otp = '".$_POST['enable_otp']."',
							page_id = '".$_POST['page']."',
							delete_otp_data_after = '".$_POST['delete_otp_data_after']."'
						WHERE id=1"));  

					}else{
						$result = $wpdb->insert($table_otp_login_register_settings, array(
							'enable_otp' => $_POST['enable_otp'],
							'page_id' => $_POST['page'],
							'delete_otp_data_after' => $_POST['delete_otp_data_after'],
						));
					}

					if($result){
						$redirect_url = admin_url( '/admin.php?page=sslcare-otp-login-register' );
					}
                    
                    ?>
                    
                    <script>
                        document.location.href = "<?php echo $redirect_url ?>";
                    </script>
                    
                    <?php
                }
            ?>

			<div class="card card-custom">
              	<div class="card-body">
					<h2>Guidelines</h2>
					<ul>
						<li>1. Create a page and place this shortcode "[sslcare_otp_login_register]" to the Page.</li>
						<li>2. From below option find Set Login Register Page Here Option</li>
						<li>3. Set this page you created as Login Register Page</li>
					</ul>
				</div>
			</div>		
			<!-- form -->
            <div class="card card-custom">
              <div class="card-body">
                <form id="formOtpLoginRegisterSettings" action="" method="POST" autocomplete="off">
				  <div class="form-group-custom">
                    <label for="title">Enable OTP Login</label> 
					<select id="enable_otp" name="enable_otp" required>
						<option value="">Select an option</option>
						<option value='No' <?php if(isset($checkData->enable_otp) && $checkData->enable_otp == 'No') { ?>selected<?php } ?>>No</option>
						<option value='Yes' <?php if(isset($checkData->enable_otp) && $checkData->enable_otp == 'Yes') { ?>selected<?php } ?>>Yes</option>
					</select>	
                  </div>
                  <div class="form-group-custom">
                    <label for="title">Set Login Register Page Here</label> 
					<?php 
					$pages = get_pages();
					// echo $checkData->page_id;
					?>
					<select id="page" name="page" required>
						<option value="">Select a Page</option>
						<?php foreach ($pages as $page) { ?>
						<option value='<?php echo $page->ID; ?>'<?php if(isset($checkData->page_id) && $checkData->page_id == $page->ID) { ?>selected<?php } ?>><?php echo $page->post_title; ?></option>
						<?php } ?>
					</select>	
                  </div>
				  <div class="form-group-custom">
                    <label for="title">Delete OTP Data after</label> 
					<div style="display:flex; flex-direction:row; align-items:center">
						<div><input type="number" name="delete_otp_data_after" id="delete_otp_data_after" value="<?php echo $checkData->delete_otp_data_after ?? "" ?>"></div><div style="padding-left:20px;">Hour</div>
					</div>
					<div style="font-style:italic; font-size:12px;">If it is empty OTP Data will not be deleted</div>
                  </div>
				  <button class="page-title-action-custom success" name="submit_setting">Save</button>
                </form>
              </div><!--/card-block-->
            </div><!-- /form card Add a Job -->
		</div>	
	<?php		
	}

	function all_otp_data_page_content(){ ?>
		<div class="admin-custom-container">
			<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
				<h1 class="title"><?php esc_html_e( 'All OTP Data', 'my-plugin-textdomain' ); ?></h1>
			</div>
			<!---Directory Link of DataTables---->
			<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url( __FILE__ ) ?>/lib/asset/css/jquery.dataTables.min.css">
			<script type="text/javascript" charset="utf8" src="<?php echo plugin_dir_url( __FILE__ ) ?>/lib/asset/js/jquery.dataTables.min.js"></script>
			<script>
				jQuery(document).ready( function () {
					jQuery('#otp_data_table').DataTable();
				} );
			</script>
			<?php 
				global $wpdb;
				$table_name = $wpdb->prefix . "sslcare_otp";
				$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY `id` DESC;");
				// echo "<pre>";
				// print_r($results);
			?>
			<table id="otp_data_table" class="table dataTable no-footer">
				<thead>
					<tr>
					    <th>SN</th>
					    <th>Phone</th>
					    <th>OTP</th>
					    <th>Captcha</th>
						<th>Quantity</th>
						<th>IP Address</th>
						<th>Sending Time</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i=1;
					foreach($results as $result){ 
					?>	
					<tr id="<?php echo $result->id; ?>">
					    <td><?php echo $i; ?></td>
					    <td><?php echo $result->phone; ?></td>
					    <td><?php echo $result->otp; ?></td>
					    <td><?php echo $result->cap_data; ?></td>
					    <td><?php echo $result->quantity; ?></td>
						<td><?php echo $result->ip_address; ?></td>
						<td><?php echo $result->sending_time; ?></td>
					</tr>
					<?php	
					$i++;
					}
					?>
				</tbody>
			</table>
		</div>
	<?php	
	}
	

	function update_user_phone_from_csv($csv_file_path, $batch_size = 500) {
		global $wpdb;
		$output = "";  // Message to store summary updates
		$error_messages = "";  // Message to store any errors
		$is_header = true;
		$processed_numbers = [];  // To track phone numbers processed within the session
		$current_row = 0;
		$batch_count = 0;
	
		if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if ($is_header) {
					$is_header = false;
					continue;  // Skip the header row
				}
	
				$current_row++;
	
				$user_id = intval(trim($data[1]));  // User ID from the second column
				$phone = trim($data[4]);  // Phone from the third column
	
				if (empty($user_id) || empty($phone)) {
					continue;
				}
	
				// Check and add leading zero if missing
				if (strlen($phone) < 11 && !preg_match('/^0/', $phone)) {
					$phone = '0' . $phone;
				}
	
				// Check if this phone number was already processed in the CSV upload
				if (in_array($phone, $processed_numbers)) {
					$error_messages .= "Phone number $phone is duplicated in the CSV file and cannot be assigned again.<br>";
					continue;
				}
	
				// Check if the user exists by User ID
				$user = get_user_by('ID', $user_id);
				if ($user) {
					// Check if the phone number is already used by another user
					$existing_phone_user = $wpdb->get_var($wpdb->prepare(
						"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'sslcare_login_phone' AND meta_value = %s",
						$phone
					));
	
					if ($existing_phone_user && $existing_phone_user != $user_id) {
						$error_messages .= "Phone number $phone is already assigned to another user (User ID: $existing_phone_user) and cannot be duplicated.<br>";
						continue;
					}
	
					// Update or add the phone number for the current user
					$existing_phone = get_user_meta($user_id, 'sslcare_login_phone', true);
					if ($existing_phone !== '') {
						update_user_meta($user_id, 'sslcare_login_phone', $phone);
					} else {
						add_user_meta($user_id, 'sslcare_login_phone', $phone);
					}
	
					// Add the phone number to the processed list for this session
					$processed_numbers[] = $phone;
				} else {
					$error_messages .= "No user found with User ID $user_id.<br>";
				}
	
				// Batch processing: pause after each batch
				if ($current_row % $batch_size == 0) {
					$batch_count++;
					fclose($handle);  // Close and reopen to reset pointer
					sleep(1);  // Optional: small delay to avoid server overload
					$handle = fopen($csv_file_path, "r");  // Reopen for the next batch
					for ($i = 0; $i <= $current_row; $i++) { fgetcsv($handle); }  // Skip already processed rows
				}
			}
			fclose($handle);
		} else {
			$error_messages .= "Unable to open the CSV file.<br>";
		}
	
		// Display summary message with collapsible error details if there are errors
		echo "<div class='notice notice-success' style='margin:0; margin-bottom:10px;'><p>CSV upload successful. Processed in $batch_count batches.</p></div>";
	
		if (!empty($error_messages)) {
			echo "<div class='notice notice-error' style='margin:0; margin-bottom:20px;'>
					<p>
						<strong>Some errors occurred during processing.</strong> 
						<a href='#' onclick='toggleErrorMessages(); return false;'>Check Errors</a>
					</p>
					<div id='error-messages' style='display:none; margin-top: 10px; max-height:150px; overflow:auto;'>
						$error_messages
					</div>
				  </div>";
			echo "<script>
					function toggleErrorMessages() {
						var errorDiv = document.getElementById('error-messages');
						if (errorDiv.style.display === 'none') {
							errorDiv.style.display = 'block';
						} else {
							errorDiv.style.display = 'none';
						}
					}
				  </script>";
		}
	}	
	
	
	function generate_sample_csv_file() {
		global $wpdb;
	
		// Query to join wp_users and wp_usermeta to get ID, email, username, and sslcare_login_phone
		$results = $wpdb->get_results("
			SELECT u.ID as user_id, u.user_login AS username, u.user_email AS email, um.meta_value AS phone
			FROM {$wpdb->users} AS u
			LEFT JOIN {$wpdb->usermeta} AS um ON u.ID = um.user_id AND um.meta_key = 'sslcare_login_phone'
			ORDER BY u.ID DESC
		");
	
		// Define the CSV file path in the uploads directory with a unique name
		$upload_dir = wp_upload_dir();
	
		// Generate a unique filename using the current timestamp or a hash
		$unique_filename = 'user_mobile_numbers_' . md5(home_url()) . '.csv';  // Using site URL hash as an ID
		$csv_file_path = $upload_dir['basedir'] . '/' . $unique_filename;
	
		// Open the file for writing
		$file = fopen($csv_file_path, 'w');
	
		// Add the header row with the new columns
		fputcsv($file, ['SN', 'User ID', 'Username', 'User Email Address', 'Mobile Number']);
	
		// Add data rows
		$i = 1;
		foreach ($results as $result) {
			$phone = $result->phone ? $result->phone : '';
			fputcsv($file, [$i, $result->user_id, $result->username, $result->email, $phone]);
			$i++;
		}
	
		// Close the file
		fclose($file);
	
		// Return the file URL for download
		return $upload_dir['baseurl'] . '/' . $unique_filename;
	}
	

	function all_csv_upload_mobile_number_page_content(){ ?>
		<div class="admin-custom-container">
			<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
				<h1 class="title"><?php esc_html_e( 'CSV Upload Mobile Number Area', 'my-plugin-textdomain' ); ?></h1>
			</div>
			<h2>Update User Phone Numbers from CSV</h2>
			<p>
				<i><strong><span style="color:#da0000">Make sure to back up the entire database before moving forward. Do it at your own risk, or reach out to us if you need help.</span><strong><br>Download this 
				<a href="<?php echo generate_sample_csv_file(); ?>">CSV File</a>. This should be the format of the CSV file. Mobile Number should be 11 Digits or without 0 it can be 10 Digits. Now update the Mobile number in that CSV file and Upload it.</i>
			</p>
			<div style="display:block; padding:10px; background-color:#dbdbdb; margin-bottom:40px;">
				<form method="post" enctype="multipart/form-data">
					<input type="file" name="csv_file" accept=".csv">
					<button type="submit" name="update_phones" class="button button-primary">Update Mobile Numbers</button>
				</form>
			</div>
			<?php
			// Run the update function if the form is submitted
			if (isset($_POST['update_phones'])) {
				if (!empty($_FILES['csv_file']['tmp_name'])) {
					$csv_file_path = $_FILES['csv_file']['tmp_name'];
					update_user_phone_from_csv($csv_file_path); // Call the function with uploaded CSV file
				} else {
					echo "<p style='color:red;'>Please upload a CSV file.</p>";
				}
			}
			?>
			<!---Directory Link of DataTables---->
			<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url( __FILE__ ) ?>/lib/asset/css/jquery.dataTables.min.css">
			<script type="text/javascript" charset="utf8" src="<?php echo plugin_dir_url( __FILE__ ) ?>/lib/asset/js/jquery.dataTables.min.js"></script>
			<script>
				jQuery(document).ready( function () {
					jQuery('#csv_mobile_number_data_table').DataTable();
				} );
			</script>
			<?php 
				global $wpdb;
	
				// Query to join wp_users and wp_usermeta to get ID, username, email, and sslcare_login_phone
				$results = $wpdb->get_results( "
					SELECT u.ID as user_id, u.user_login AS username, u.user_email AS email, um.meta_value AS phone
					FROM {$wpdb->users} AS u
					LEFT JOIN {$wpdb->usermeta} AS um ON u.ID = um.user_id AND um.meta_key = 'sslcare_login_phone'
					ORDER BY u.ID DESC
				" );
			?>

			<style>
				table.dataTable tbody th, table.dataTable tbody td{
					font-weight:normal;
				}
			</style>
	
			<table id="csv_mobile_number_data_table" class="table dataTable no-footer">
				<thead>
					<tr>
						<th>SN</th>
						<th>User ID</th>
						<th>Username</th>
						<th>User Email Address</th>
						<th>Mobile Number</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					foreach($results as $result) { 
					?>    
					<tr id="<?php echo $result->user_id; ?>">
						<td><?php echo $i; ?></td>
						<td><?php echo $result->user_id; ?></td>
						<td><?php echo $result->username; ?></td>
						<td><?php echo $result->email; ?></td>
						<td><?php echo $result->phone ? $result->phone : '---'; ?></td>
					</tr>
					<?php    
						$i++;
					}
					?>
				</tbody>
			</table>
	
		</div>
	<?php	
	}
	

	function enqueue_my_script() {
		wp_enqueue_script('otp-login-register-script', plugin_dir_url( __FILE__ ) . 'lib/asset/js/otp-login-register.js?v=4.1', array('jquery'), '1.0', true);
	
		// Localize the script with the nonce
		wp_localize_script('otp-login-register-script', 'ajax_otp_login_object', array('plugin_directory' => plugin_dir_url(__FILE__), 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('my_unique_login_nonce_action')));

		wp_localize_script('otp-login-register-script', 'ajax_otp_register_object', array('plugin_directory' => plugin_dir_url(__FILE__), 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('my_unique_register_nonce_action')));

		wp_localize_script('otp-login-register-script', 'ajax_otp_send_object', array('plugin_directory' => plugin_dir_url(__FILE__), 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('my_unique_otp_nonce_action')));

		wp_localize_script('otp-login-register-script', 'ajax_final_login_object', array('plugin_directory' => plugin_dir_url(__FILE__), 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('my_unique_otp_nonce_action')));

	}
	add_action('wp_enqueue_scripts', 'enqueue_my_script');

	add_action('wp_ajax_otp_login_ajax_action', 'otp_login_ajax_callback');
	add_action('wp_ajax_nopriv_otp_login_ajax_action', 'otp_login_ajax_callback');

	add_action('wp_ajax_otp_register_ajax_action', 'otp_register_ajax_callback');
	add_action('wp_ajax_nopriv_otp_register_ajax_action', 'otp_register_ajax_callback');

	add_action('wp_ajax_otp_send_ajax_action', 'otp_send_ajax_callback');
	add_action('wp_ajax_nopriv_otp_send_ajax_action', 'otp_send_ajax_callback');

	add_action('wp_ajax_final_login_ajax_action', 'final_login_ajax_callback');
	add_action('wp_ajax_nopriv_final_login_ajax_action', 'final_login_ajax_callback');

	function otp_login_ajax_callback() {
		// Verify the nonce
		if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'my_unique_login_nonce_action')) {
			// Nonce is valid, perform your AJAX processing here
			if(isset($_POST['login'])){
				if(isset($_POST['phone']) && isset($_POST['capData'])){

					global $api_hash_token;
					global $api_url;
					global $api_username;
					global $api_password;
					global $sslcare_api_sid;
					global $sslcare_otp_login_register_template;
	
					$client_ip = $_SERVER['REMOTE_ADDR'];
					$date = date('Y-m-d H:i:s');
	
					$phone = $_POST['phone'];
	
					$users = get_users(array(
						'meta_key' => 'sslcare_login_phone',
						'meta_value' => $phone,
						'number'      => 1,  // Limit the query to one user
						'count_total' => false, // Don't calculate total users, only need one
					));
	
					if(empty($users)){
						$dataCheck = 'user-not-found';
						echo wp_send_json($dataCheck);
						exit();
					}else{
						$user_id = $users[0]->ID; // Get the user ID
						// Check if the user has no role
						$user = get_userdata($user_id);
						$user_roles = $user->roles;
						if (empty($user_roles)) {
							// User has no role
							// echo 'User has no role.';
							$dataCheck = 'user-has-no-role';
							echo wp_send_json($dataCheck);
							exit();
						}
						if(!empty($users[0]->sslcare_login_phone) && $users[0]->sslcare_login_phone == $phone){
		
							$randnum = time().uniqid(rand(11111,99999));
							$otp = rand(11111,99999);
	
							// Replace {{otp}} with dynamic code
							$outputString = str_replace('{{otp}}', $otp, $sslcare_otp_login_register_template);
	
							// echo wp_send_json($outputString);
							// exit();
							$params = json_encode([
								
									"api_token" => $api_hash_token,
									"sid" => $sslcare_api_sid,
									"msisdn" => $phone,
									"sms" => $outputString,
									"csms_id" => $randnum
							]);
	
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => $api_url,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS => $params,
								CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Accept:application/json'
								),
							));
						
							$response = curl_exec($curl);
						
							curl_close($curl);
							// $dataNow = wp_send_json($response);
							$assocArray = json_decode($response, true);
							// print_r($assocArray);
		
							global $wpdb;
							$sslcare_otp_table_name = $wpdb->prefix . "sslcare_otp";
							$check_data = $wpdb->get_row( "SELECT * FROM $sslcare_otp_table_name WHERE phone = '".$assocArray['smsinfo'][0]['msisdn']."'");
		
							if($check_data){
								$quantity_now = $check_data->quantity + 1;
								if($quantity_now <=20){
									$result = $wpdb->query($wpdb->prepare("UPDATE $sslcare_otp_table_name SET 
										otp = '".$otp."',
										cap_data = '".$_POST['capData']."',
										ip_address = '".$client_ip."',
										quantity = '".$quantity_now."',
										sending_time = '".$date."'
									WHERE id=$check_data->id"));
								}else{
									$dataCheck = 'sms-limit-exceeded';
									echo wp_send_json($dataCheck);
									exit();
								}
							}else{
								$result = $wpdb->insert($sslcare_otp_table_name, array(
									'phone' => $assocArray['smsinfo'][0]['msisdn'],
									'otp' => $otp,
									'cap_data' => $_POST['capData'],
									'quantity' => 1,
									'ip_address' => $client_ip,
									'sending_time' => $date,
								));
							}
							if($result){
								$twoMinutesLater = date("Y-m-d H:i:s", strtotime($date . " +2 minutes"));
								$dataArray = [
									"status_code" => $assocArray['status_code'],
									"sms_status" => $assocArray['smsinfo'][0]['sms_status'],
									"phone" => $assocArray['smsinfo'][0]['msisdn'],
									'cap_data' => $_POST['capData'],
									"future_time" => $twoMinutesLater,
									"otp_sent_time" => $date,
								];
								echo wp_send_json($dataArray);
							}else{
								$dataCheck = 'otp-not-sent';
								echo wp_send_json($dataCheck);
								exit();
							}
						}
					}
				}
			}
			// When username and password is to use to login
			if(isset($_POST['login_with_username'])){
				$username = sanitize_user($_POST['username']);
				$password = sanitize_text_field($_POST['password']);
				
				$credentials = array(
					'user_login'    => $username,
					'user_password' => $password,
					'remember'      => true
				);

				$user = wp_signon($credentials, false);

				if (is_wp_error($user)) {
					$dataCheck = 'login-with-username-failed';
					echo wp_send_json($dataCheck);
				} else {
					$dataCheck = 'login-with-username-success';
					echo wp_send_json($dataCheck);
				}

				die();
			}
		} else {
			// Nonce is not valid
			echo 'Nonce verification failed.';
		}

		wp_die(); // Always include this at the end of your AJAX callback
	}

	function otp_register_ajax_callback() {
		if(isset($_POST['register'])){
			if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'my_unique_register_nonce_action')) {
				// Nonce is valid, process the AJAX request
				// Your AJAX processing code here
				if(isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['capData'])){

					$password = $_POST['password'];
					// Check if the password meets the specified requirements
					if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
						$dataCheck = 'weak-password';
						echo wp_send_json($dataCheck);
						exit();
					}

					global $api_hash_token;
					global $api_url;
					global $api_username;
					global $api_password;
					global $sslcare_api_sid;
					global $sslcare_otp_login_register_template;

					$client_ip = $_SERVER['REMOTE_ADDR'];
					$date = date('Y-m-d H:i:s');

					$email = $_POST['email'];
					$phone = $_POST['phone'];

					$users = get_users(array(
						'meta_key' => 'sslcare_login_phone',
						'meta_value' => $phone,
					));

					$user = get_user_by('email', $email);
					// Check if a user with the given email is found
					if ($user) {
						$dataCheck = 'user-already-exists';
						echo wp_send_json($dataCheck);
						exit();
					}
					if(!empty($users[0]->sslcare_login_phone)){
						$dataCheck = 'user-already-exists';
						echo wp_send_json($dataCheck);
						exit();
					}
					
					$randnum = time().uniqid(rand(11111,99999));
					$otp = rand(11111,99999);

					// Replace {{otp}} with dynamic code
					$outputString = str_replace('{{otp}}', $otp, $sslcare_otp_login_register_template);

					// echo wp_send_json($outputString);
					// exit();
					$params = json_encode([
						
							"api_token" => $api_hash_token,
							"sid" => $sslcare_api_sid,
							"msisdn" => $phone,
							"sms" => $outputString,
							"csms_id" => $randnum
					]);

					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_URL => $api_url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => $params,
						CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json',
						'Accept:application/json'
						),
					));
				
					$response = curl_exec($curl);
				
					curl_close($curl);
					// $dataNow = wp_send_json($response);
					$assocArray = json_decode($response, true);
					// print_r($assocArray);

					global $wpdb;
			
					$sslcare_otp_table_name = $wpdb->prefix . "sslcare_otp";
				
					$check_data = $wpdb->get_row( "SELECT * FROM $sslcare_otp_table_name WHERE phone = '".$assocArray['smsinfo'][0]['msisdn']."'");

					if($check_data){
						$quantity_now = $check_data->quantity + 1;
						if($quantity_now <=20){
							$result = $wpdb->query($wpdb->prepare("UPDATE $sslcare_otp_table_name SET 
								otp = '".$otp."',
								cap_data = '".$_POST['capData']."',
								ip_address = '".$client_ip."',
								quantity = '".$quantity_now."',
								sending_time = '".$date."'
							WHERE id=$check_data->id"));
						}else{
							$dataCheck = 'sms-limit-exceeded';
							echo wp_send_json($dataCheck);
							exit();
						}
					}else{
						$result = $wpdb->insert($sslcare_otp_table_name, array(
							'phone' => $assocArray['smsinfo'][0]['msisdn'],
							'otp' => $otp,
							'cap_data' => $_POST['capData'],
							'quantity' => 1,
							'ip_address' => $client_ip,
							'sending_time' => $date,
						));
					}
					if($result){
						$twoMinutesLater = date("Y-m-d H:i:s", strtotime($date . " +2 minutes"));
						$dataArray = [
							"status_code" => $assocArray['status_code'],
							"sms_status" => $assocArray['smsinfo'][0]['sms_status'],
							"phone" => $assocArray['smsinfo'][0]['msisdn'],
							"email" => $email,
							"reg_password_sslcare" => $password,
							"cap_data" => $_POST['capData'],
							"future_time" => $twoMinutesLater,
							"otp_sent_time" => $date,
						];
						echo wp_send_json($dataArray);
						exit();
					}else{
						$dataCheck = 'otp-not-sent';
						echo wp_send_json($dataCheck);
						exit();
					}
				}
				
			} else {
				// Nonce is not valid, return an error response or take appropriate action
				wp_send_json_error('Nonce verification failed.');
			}
		}
	}

	function otp_send_ajax_callback() {
		if(isset($_POST['register']) || isset($_POST['login'])){
			if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'my_unique_otp_nonce_action')) {
				// Nonce is valid, process the AJAX request
				// Your AJAX processing code here
				if(isset($_POST['phone']) && isset($_POST['otp']) && isset($_POST['capData'])){
		
					$phone = $_POST['phone'];
					$otp = $_POST['otp'];
					$capData = $_POST['capData'];
					$password = $_POST['password'] ? $_POST['password'] : '';
				
					global $wpdb;
				
					$sslcare_otp_table_name = $wpdb->prefix . "sslcare_otp";
				
					$result = $wpdb->get_row( "SELECT * FROM $sslcare_otp_table_name WHERE phone = $phone AND otp = $otp AND cap_data = '".$capData."'");
				
					if($result){
						if($result->cap_data === $capData){
							$sslcare_login_phone_compatible =  substr($phone, 2);
							$users = get_users(array(
								'meta_key' => 'sslcare_login_phone',
								'meta_value' => $sslcare_login_phone_compatible,
							));

							if(!empty($users)){
							    $user_id = $users[0]->ID; // Get the user ID
							    $user_data = get_userdata($user_id);
							    $user_email = $user_data->user_email;
								$dataArray = [
									"email" => $user_email,
									"phone" => $phone,
									"reg_password_sslcare" => $password,
									// "otp" => $result->otp,
								];
							}else{
								$dataArray = [
									"email" => $_POST['email'],
									"phone" => $phone,
									"reg_password_sslcare" => $password,
									// "otp" => $result->otp,
								];
							}
							// Current datetime
							$currentDatetime = new DateTime();
			
							// Another datetime to compare (e.g., a stored datetime from the database)
							$storedDatetime = new DateTime($result->sending_time); // Replace with your stored datetime
			
							// Calculate the difference in minutes
							$interval = $currentDatetime->diff($storedDatetime);
							$minutesDifference = $interval->i; // Minutes difference
			
							if ($minutesDifference < 2) {
								// echo "Data inserted Successfully";
								
								echo wp_send_json($dataArray);
								// echo 'success';
							}else{
								$dataCheck = 'otp-expired';
								echo wp_send_json($dataCheck);
								exit();
							}
						}
					}else{
						$dataCheck = 'otp-incorrect';
							echo wp_send_json($dataCheck);
						exit();
					}
				}
			} else {
				// Nonce is not valid, return an error response or take appropriate action
				wp_send_json_error('Nonce verification failed.');
			}
		}
	}

	function final_login_ajax_callback() {

		//This is for Register only
		if(isset($_POST['register'])){
			if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'my_unique_otp_nonce_action')) {
				if(isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['register'])){

					global $wpdb;
				
					// Define user information
					$phone = $_POST['phone'];
					$email = $_POST['email'];
					$password = $_POST['password'];
					$digits = 9;
					$username = 'user_'.rand(pow(10, $digits-1), pow(10, $digits)-1);
					// $username = 'custom_user';
					// $password = 'U8*I6J*V$gKkKGT*';
				
					// Create a new user
					$user_id = wp_insert_user(array(
						'user_login' => $username,
						'user_pass' => $password,
						'user_email' => $email,
						'role' => 'customer',  // Set the desired user role (e.g., 'customer' or 'subscriber')
					));
				
					if (is_wp_error($user_id)) {
						// User creation failed, handle the error
						// echo 'User creation failed: ' . $user_id->get_error_message();
						$dataCheck = 'user-already-exists';
						echo wp_send_json($dataCheck);
						exit();
					} else {
						$sslcare_login_phone_compatible =  substr($phone, 2);
						// User creation successful
						// update_user_meta( $user_id, "billing_first_name", 'First Name of Zia' );
						// update_user_meta( $user_id, "billing_last_name", 'Last Name of Zia' );
						update_user_meta( $user_id, "sslcare_login_phone", $sslcare_login_phone_compatible );
						// update_user_meta( $user_id, 'billing_phone', $sslcare_login_phone_compatible);
						// update_user_meta( $user_id, "billing_email", $email );
						// echo 'User created with ID: ' . $user_id;
						$user_data = array(
							'user_login' => $username, // Replace with the custom username.
							'user_email' => $email, // Replace with the custom email.
							// 'user_pass' => '', // Leave the password empty.
						);
						$user = get_user_by('login', $user_data['user_login']);
						wp_set_current_user($user->ID, $user->user_login);
						wp_set_auth_cookie($user->ID);
						echo wp_send_json($user_data);
					}
				}
			}
		}

		//This is for Login only
		if(isset($_POST['login'])){
			if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'my_unique_otp_nonce_action')) {
				if(isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['login'])){

					global $wpdb;
				
					$sslcare_otp_table_name = $wpdb->prefix . "sslcare_otp";
				
					// Define user information
					$phone = $_POST['phone'];
					$email = $_POST['email'];
				
					$user = get_user_by('email', $email);
					wp_set_current_user($user->ID, $user->user_login);
					wp_set_auth_cookie($user->ID);
				
					$user_data = array(
						'user_login' => $user->user_login, // Replace with the custom username.
						'user_email' => $user->user_email, // Replace with the custom email.
				        // 'user_pass' => '', // Leave the password empty.
					);

					$sslcare_login_phone_compatible =  substr($phone, 2);

					// update_user_meta( $user->ID, 'billing_phone', $sslcare_login_phone_compatible);
					
					echo wp_send_json($user_data);
				
				}
			}
		}
	}


	function sslcare_otp_login_register_shortcode_function($atts) { 
		ob_start();
		// Get the path to the plugin directory
		$plugin_dir_path = plugin_dir_path( __FILE__ );
		// Include your plugin file
		include_once $plugin_dir_path . 'lib/sslcare-otp-login-register.php';
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
		?>
	<?php 
	}
	add_shortcode('sslcare_otp_login_register', 'sslcare_otp_login_register_shortcode_function');

	function custom_login_redirect($redirect_to, $request, $user) {
		// Is the user an administrator?
		if (isset($user->roles) && is_array($user->roles) && in_array('administrator', $user->roles)) {
			// Redirect administrators to the admin dashboard
			return admin_url();
		} else {
			// Redirect other users to the home URL
			return home_url();
		}
	}
	add_filter('login_redirect', 'custom_login_redirect', 10, 3);


	add_action('template_redirect', 'custom_my_account_redirect');

	function custom_my_account_redirect() {
		global $wpdb;
		$table_otp_login_register_settings = $wpdb->prefix . "sslcare_otp_login_register_settings";
		$otp_login_register_custom_page = $wpdb->get_row( "SELECT * FROM $table_otp_login_register_settings WHERE id = 1"); 
		if(!empty($otp_login_register_custom_page) && $otp_login_register_custom_page->enable_otp == 'Yes'){
			$page_id = $otp_login_register_custom_page->page_id;
			if($page_id){
				// Get the page permalink.
				$page_permalink = get_permalink($page_id);
				if (!is_user_logged_in() && is_account_page() && !is_wc_endpoint_url('lost-password')) {
					// Replace 'your-custom-url' with the URL you want to redirect users to.
					wp_redirect($page_permalink);
					exit();
				}
			}
		}
	}

	// Display the Phone field on the account details page
	function add_sslcare_login_phone_field() {
		global $wpdb;
		$table_otp_login_register_settings = $wpdb->prefix . "sslcare_otp_login_register_settings";
		$otp_login_register_custom_page = $wpdb->get_row( "SELECT * FROM $table_otp_login_register_settings WHERE id = 1"); 
		if($otp_login_register_custom_page->enable_otp == 'Yes'){
		?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="sslcare_login_phone"><?php _e('Phone', 'woocommerce'); ?> (Valid format eg: 01XXXXXXXXX 11 digit only)<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="sslcare_login_phone" id="sslcare_login_phone" pattern="01[3-9]\d{8}" value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'sslcare_login_phone', true)); ?>" required />
		</p>
		<?php
		}
	}

	add_action('woocommerce_edit_account_form', 'add_sslcare_login_phone_field');


	// Save Phone to User Meta and Ensure Uniqueness:

	$user_id = get_current_user_id(); 
	function save_sslcare_login_phone_field($user_id) {
		$sslcare_login_phone = isset($_POST['sslcare_login_phone']) ? sanitize_text_field($_POST['sslcare_login_phone']) : '';
	
		// Check if the Phone is already associated with another user
		$existing_users = get_users(array('meta_key' => 'sslcare_login_phone', 'meta_value' => $sslcare_login_phone));

		$phone_pattern = "/^01[3-9]\d{8}$/";

		if (!empty($sslcare_login_phone)) {
			if (!preg_match($phone_pattern, $sslcare_login_phone)) {
				wc_add_notice(__('Phone format is not supported. Please try to input valid Phone number eg: 01XXXXXXXXX (11 digit only)'), 'error');
			}else{
				if (!empty($existing_users)) {
					$sslcare_login_phone_of_this_user = get_user_meta($user_id, 'sslcare_login_phone', true);
					if($sslcare_login_phone_of_this_user === $sslcare_login_phone){
						update_user_meta($user_id, 'sslcare_login_phone', $sslcare_login_phone);
					}else{
						wc_add_notice(__('The Phone you entered is already in use by another user. Please choose a different Phone Number', 'woocommerce'), 'error');
					}
				}else{
					update_user_meta($user_id, 'sslcare_login_phone', $sslcare_login_phone);
					// update_user_meta($user_id, 'billing_phone', $sslcare_login_phone);
				}
			}
		}else{
			echo "Error";
		}
	}
	
	add_action('woocommerce_save_account_details', 'save_sslcare_login_phone_field');

	// Allow saving account details without entering password
	add_filter('woocommerce_save_account_details_requires_password', '__return_false');


	function custom_delete_table_data() {
		
		global $wpdb;

		$sslcare_otp_login_register_settings_table =  $wpdb->prefix . 'sslcare_otp_login_register_settings';
		$sslcare_otp_login_register_settings = $wpdb->get_row( "SELECT * FROM $sslcare_otp_login_register_settings_table WHERE id =1");
		$hour_delete_data = $sslcare_otp_login_register_settings->delete_otp_data_after;

		if($hour_delete_data > 0){
			// Replace 'your_table_name' with the actual name of your table
			$sslcare_otp_table_name = $wpdb->prefix . 'sslcare_otp';
				
			// $result = $wpdb->query($wpdb->prepare("UPDATE $sslcare_otp_table_name SET 
			// 	quantity = 0
			// WHERE quantity=5"));
			// Delete all data from the table

			$all_data = $wpdb->get_results( "SELECT * FROM $sslcare_otp_table_name");
			foreach($all_data as $single_data){
				$todayDate = date("Y-m-d H:i:s");
				$sending_time = $single_data->sending_time;

				$expiration_time = strtotime($sending_time); // Replace this with your actual timestamp

				// Get the timestamp 3 hours later
				$expiration_time = $expiration_time + (($hour_delete_data + 1) * 3600);

				// Convert the timestamp to a readable date and time
				$expiration_time = date("Y-m-d H:i:s", $expiration_time);

				// Create DateTime objects for the two dates
				$dateTime1 = strtotime($todayDate);
				$dateTime2 = strtotime($expiration_time);

				$secondsDifference = $dateTime2 - $dateTime1;

				// Convert seconds to hours
				$hoursDifference = intval($secondsDifference / 3600);

				// echo "date1" .$dateTime1;
				// echo "date2" .$dateTime2;
				// echo "Difference of hours is: " . $hoursDifference . " hours";

				if($hoursDifference <= 0){
					$wpdb->query("DELETE FROM $sslcare_otp_table_name WHERE id=$single_data->id");
				}
			}
		}
	}
	// Schedule the event to run daily
	add_action('wp', 'schedule_custom_delete');
	function schedule_custom_delete() {
		if (!wp_next_scheduled('custom_delete_table_data_event')) {
			wp_schedule_event(time(), 'daily', 'custom_delete_table_data_event');
		}
	}
	// Hook your function to the scheduled event
	add_action('custom_delete_table_data_event', 'custom_delete_table_data');

?>