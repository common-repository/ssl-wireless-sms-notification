<?php
/**
 * Admin settings option
 */

namespace Sslcare\Admin\Setting;

class Sslcare_Admin_Setting
{
	public function __construct() {
        add_action( 'admin_menu', array( $this, 'sslcare_create_side_menu' ) );
        add_action( 'admin_init', array( $this, 'sslcare_initialize_settings' ));
    }

	public function sslcare_create_side_menu() {

		global $plugin_slug;

	    add_menu_page(
	        __('SSL Wireless', $plugin_slug),
	        __('SSL Wireless', $plugin_slug),
	        'administrator',
	        'sslcare-notification',
	        array( $this, 'sslcare_sms_settings')
	    );

	    add_submenu_page(
	        'sslcare-notification',
	        __('Woocommerce Transactional SMS', $plugin_slug),
	        __('SMS Report', $plugin_slug),
	        'administrator',
	        'sslcare-report',
	        array( $this, 'sslcare_sms_report')
	    );
	}

	# Settings Page Content

	public function sslcare_sms_settings() {
	?>
	    <div class="wrap">
	        <h2>SSL Care Settings</h2>

	        <?php settings_errors(); ?>

	        <form method="post" action="options.php">
	            <?php settings_fields( 'sslcare_notification' ); ?>
	            <?php do_settings_sections( 'sslcare_notification' ); ?>
	            <?php submit_button(); ?>
	        </form>

	    </div>

		<!-- JavaScript to handle conditional display of fields based on platform selection When iSMS Plus is selected or selecting it Api Username and Api Password Field will be hidden -->
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function() {
				var platformSelect = document.querySelector('[name="sslcare_notification[sslcare_api_version]"]');
				var apiUserField = document.querySelector('[name="sslcare_notification[api_username]"]').parentNode.parentNode;
				var apiPasswordField = document.querySelector('[name="sslcare_notification[api_password]"]').parentNode.parentNode;
				var apiHashTokenField = document.querySelector('[name="sslcare_notification[api_hash_token]"]').parentNode.parentNode;
				var apiUrlField = document.querySelector('[name="sslcare_notification[api_url]"]');
				
				function toggleApiFields() {
					if (platformSelect.value === 'ismsplus') {
						apiUserField.style.display = 'none';
						apiPasswordField.style.display = 'none';
						apiHashTokenField.style.display = 'table-row';
						apiUrlField.value = 'https://smsplus.sslwireless.com/api/v3/send-sms';
					} else {
						apiUserField.style.display = 'table-row';
						apiPasswordField.style.display = 'table-row';
						apiHashTokenField.style.display = 'none';
						apiUrlField.value = 'https://sms.sslwireless.com/pushapi/dynamic/server.php';
					}
				}

				platformSelect.addEventListener('change', toggleApiFields);
				toggleApiFields();  // Initial check
			});
		</script>

	<?php
	}

	public function sslcare_sms_report() {
	?>
	    <div class="wrap">
	        <h2>SSLCare Transactional SMS Report</h2><hr>
	        <?php include_once( SSLW_SMS_PATH . 'lib/sslcare-woo-report.php' ); ?>
	    </div>
	<?php
	}

	public function sslcare_initialize_settings() {
		global $plugin_slug;

	    if( false == get_option( 'sslcare_notification' ) ) {
	        add_option( 'sslcare_notification' );
	    }

	    # API Configuration Section

	    add_settings_section(
	        'api_settings_section',
	        __('API Configuration', $plugin_slug),
	        array( $this, 'sslcare_notifications_callback'),
	        'sslcare_notification'
	    );

	    add_settings_field(
	        'enable_plugin',
	        __('Enable Plugin', $plugin_slug),
	        array( $this, 'sslcare_plugin_enable_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

	    add_settings_field(
	        'sslcare_api_version',
	        __('Select SSL Care Platform', $plugin_slug),
	        array( $this, 'sslcare_api_version_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

	    add_settings_field(
	        'api_hash_token',
	        __('API Hash Token', $plugin_slug),
	        array( $this, 'sslcare_api_hash_token_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

		add_settings_field(
	        'api_url',
	        __('API URL', $plugin_slug),
	        array( $this, 'sslcare_api_url_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

	    add_settings_field(
	        'api_username',
	        __('API User', $plugin_slug),
	        array( $this, 'sslcare_username_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

	    add_settings_field(
	        'api_password',
	        __('API Password', $plugin_slug),
	        array( $this, 'sslcare_password_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

	    add_settings_field(
	        'sslcare_api_sid',
	        __('SID/Stakeholder', $plugin_slug),
	        array( $this, 'sslcare_sid_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );

	    add_settings_field(
	        'enable_unicode_sms',
	        __('Unicode/Bangla SMS', $plugin_slug),
	        array( $this, 'sslcare_unicode_callback'),
	        'sslcare_notification',
	        'api_settings_section'
	    );


	    # Template Configuration Section

	    add_settings_section(
	        'template_settings_section',
	        __('SMS Template Configuration', $plugin_slug),
	        array( $this, 'template_settings_callback'),
	        'sslcare_notification'
	    );

	    add_settings_field(
	        'sslcare_pending_alert',
	        __('Order Pending Alert', $plugin_slug),
	        array( $this, 'sslcare_pending_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_pending_template',
	        __('Pending Alert Template', $plugin_slug),
	        array( $this, 'sslcare_pending_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

		add_settings_field(
	        'sslcare_processing_alert',
	        __('Order Processing Alert', $plugin_slug),
	        array( $this, 'sslcare_processing_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_processing_template',
	        __('Processing Alert Template', $plugin_slug),
	        array( $this, 'sslcare_processing_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );	    

	    add_settings_field(
	        'sslcare_onhold_alert',
	        __('Order On-Hold Alert', $plugin_slug),
	        array( $this, 'sslcare_onhold_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_onhold_template',
	        __('On-Hold Alert Template', $plugin_slug),
	        array( $this, 'sslcare_onhold_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_failed_alert',
	        __('Order Failed Alert', $plugin_slug),
	        array( $this, 'sslcare_failed_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_failed_template',
	        __('Failed Alert Template', $plugin_slug),
	        array( $this, 'sslcare_failed_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_canceled_alert',
	        __('Order Cancelled Alert', $plugin_slug),
	        array( $this, 'sslcare_canceled_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_canceled_template',
	        __('Cancelled Alert Template', $plugin_slug),
	        array( $this, 'sslcare_canceled_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );
	    
	    add_settings_field(
	        'sslcare_failed_template',
	        __('Failed Alert Template', $plugin_slug),
	        array( $this, 'sslcare_failed_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_completed_alert',
	        __('Order Completed Alert', $plugin_slug),
	        array( $this, 'sslcare_completed_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_completed_template',
	        __('Completed Alert Template', $plugin_slug),
	        array( $this, 'sslcare_completed_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_refund_alert',
	        __('Order Refund Alert', $plugin_slug),
	        array( $this, 'sslcare_refund_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_refund_template',
	        __('Refund Alert Template', $plugin_slug),
	        array( $this, 'sslcare_refund_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_partial_alert',
	        __('Order Partially Paid Alert', $plugin_slug),
	        array( $this, 'sslcare_partial_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_partial_template',
	        __('Partially Paid Alert Template', $plugin_slug),
	        array( $this, 'sslcare_partial_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_shipped_alert',
	        __('Order Shipped Alert', $plugin_slug),
	        array( $this, 'sslcare_shipped_alert_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    add_settings_field(
	        'sslcare_shipped_template',
	        __('Shipped Alert Template', $plugin_slug),
	        array( $this, 'sslcare_shipped_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

		add_settings_field(
	        'sslcare_otp_login_register_template',
	        __('OTP Login Register Alert Template', $plugin_slug),
	        array( $this, 'sslcare_otp_login_register_template_callback'),
	        'sslcare_notification',
	        'template_settings_section'
	    );

	    # Admin SMS Settings

	    add_settings_section(
	        'admin_settings_section',
	        __('Admin SMS Configuration', $plugin_slug),
	        array( $this, 'admin_sms_settings_callback'),
	        'sslcare_notification'
	    );

	    add_settings_field(
	        'sslcare_admin_sms_alert',
	        __('Order Alert For Admin', $plugin_slug),
	        array( $this, 'sslcare_admin_sms_alert_callback'),
	        'sslcare_notification',
	        'admin_settings_section'
	    );

	    add_settings_field(
	        'sslcare_admin_phone',
	        __('Admin Phone Number', $plugin_slug),
	        array( $this, 'sslcare_admin_phone_callback'),
	        'sslcare_notification',
	        'admin_settings_section'
	    );

		add_settings_field(
	        'sslcare_admin_sms_template',
	        __('Admin Alert Template', $plugin_slug),
	        array( $this, 'sslcare_admin_sms_template_callback'),
	        'sslcare_notification',
	        'admin_settings_section'
	    );	    

	    register_setting(
	        'sslcare_notification',
	        'sslcare_notification',
	        array( $this, 'sslcare_sanitize_settings')
	    );
	}

	public function sslcare_notifications_callback() {
	    echo "<hr>";
	}
		
	public function sslcare_plugin_enable_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $enable_plugin = get_option('enable_plugin');
	    if( isset( $options['enable_plugin'] ) && $options['enable_plugin'] != '' ) {
	        $enable_plugin = $options['enable_plugin'];
	    }

	    $html = '<input type="checkbox" id="enable_plugin" name="sslcare_notification[enable_plugin]" value="1"' . checked( 1, $enable_plugin, false ) . '/>';
	    $html .= '<label for="checkbox_example">Check to enable the plugin.</label>';

	    echo $html;
	}

	public function sslcare_api_version_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_api_version = get_option('sslcare_api_version');
	    if( isset( $options['sslcare_api_version'] ) && $options['sslcare_api_version'] != '' ) {
	        $sslcare_api_version = $options['sslcare_api_version'];
	    }

	   	$html = '<select name="sslcare_notification[sslcare_api_version]">';
	   	if($sslcare_api_version == 'ismsplus'){
		   	$html .= '<option value="isms">ISMS</option>';
		   	$html .= '<option value="ismsplus" selected>ISMS Plus</option>';
	   	}
	   	else
	   	{
	   		$html .= '<option value="isms" selected>ISMS</option>';
		   	$html .= '<option value="ismsplus">ISMS Plus</option>';
	   	}
	    $html .= '<label for="checkbox_example">Select SSL Care Platform</label>';

	    echo $html;
	}

	public function sslcare_api_hash_token_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $api_hash_token = '';
	    if( isset( $options['api_hash_token'] ) && $options['api_hash_token'] != '' ) {
	        $api_hash_token = $options['api_hash_token'];
	    }

	    $html = '<input type="text" name="sslcare_notification[api_hash_token]" value="' . $api_hash_token . '" size="65" placeholder="Only use for ISMS Plus Platform"/>';
	    $html .= '<br><label for="api_hash_token">Only use for ISMS Plus (Get it from Panel Profile).</label>';

	    echo $html;
	}

	public function sslcare_api_url_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $api_url = '';
	    if( isset( $options['api_url'] ) && $options['api_url'] != '' ) {
	        $api_url = $options['api_url'];
	    }

	    $html = '<input type="text" name="sslcare_notification[api_url]" value="' . $api_url . '" size="65" readonly placeholder="URL of API"/>';
	    $html .= '<br><label for="api_url">Must input this field.</label>';

	    echo $html;
	}

	public function sslcare_username_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $api_username = '';
	    if( isset( $options['api_username'] ) && $options['api_username'] != '' ) {
	        $api_username = $options['api_username'];
	    }

	    $html = '<input type="text" name="sslcare_notification[api_username]" value="' . $api_username . '" size="45" placeholder="Only use for ISMS  Platform"/>';
	    $html .= '<br><label for="api_username">API User (Provided from SSL Wireless).</label>';

	    echo $html;
	}

	public function sslcare_password_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $api_password = '';
	    if( isset( $options['api_password'] ) && $options['api_password'] != '' ) {
	        $api_password = $options['api_password'];
	    }

	    $html = '<input type="text" name="sslcare_notification[api_password]" value="' . $api_password . '" size="45" placeholder="Only use for ISMS Platform"/>';
	    $html .= '<br><label for="api_password">API Password (Provided from SSL Wireless).</label>';

	    echo $html;
	}

	public function sslcare_sid_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_api_sid = '';
	    if( isset( $options['sslcare_api_sid'] ) && $options['sslcare_api_sid'] != '' ) {
	        $sslcare_api_sid = $options['sslcare_api_sid'];
	    }

	    $html = '<input type="text" name="sslcare_notification[sslcare_api_sid]" value="' . $sslcare_api_sid . '" size="45" placeholder="Only use for ISMS & ISMS Plus Platform"/>';
	    $html .= '<br><label for="sslcare_api_sid">SID/Stakeholder (Provided from SSL Wireless).</label>';

	    echo $html;
	}
	
	public function sslcare_unicode_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $enable_unicode_sms = get_option('enable_unicode_sms');
	    if( isset( $options['enable_unicode_sms'] ) && $options['enable_unicode_sms'] != '' ) {
	        $enable_unicode_sms = $options['enable_unicode_sms'];
	    }

	    $html = '<input type="checkbox" id="enable_unicode_sms" name="sslcare_notification[enable_unicode_sms]" value="1"' . checked( 1, $enable_unicode_sms, false ) . '/>';
	    $html .= '<label for="enable_unicode_sms">Check to enable Unicode/Bangla SMS (Only for ISMS Platform).</label>';

	    echo $html;
	}


	public function template_settings_callback() {
	    echo "<hr>";
	}

	public function sslcare_pending_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_pending_alert = get_option('sslcare_pending_alert');
	    if( isset( $options['sslcare_pending_alert'] ) && $options['sslcare_pending_alert'] != '' ) {
	        $sslcare_pending_alert = $options['sslcare_pending_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_pending_alert" name="sslcare_notification[sslcare_pending_alert]" value="1"' . checked( 1, $sslcare_pending_alert, false ) . '/>';
	    $html .= '<label for="sslcare_pending_alert">Enable this field for Order Pending Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_pending_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_pending_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_pending_template'] ) && $options['sslcare_pending_template'] != '' ) {
	        $sslcare_pending_template = $options['sslcare_pending_template'];
	    }
	    
	    $html = '<textarea id="sslcare_pending_template" rows="4" cols="98" name="sslcare_notification[sslcare_pending_template]" placeholder="">' . $sslcare_pending_template . '</textarea>';
	    $html .= '<br><label for="sslcare_pending_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_processing_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_processing_alert = get_option('sslcare_processing_alert');
	    if( isset( $options['sslcare_processing_alert'] ) && $options['sslcare_processing_alert'] != '' ) {
	        $sslcare_processing_alert = $options['sslcare_processing_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_processing_alert" name="sslcare_notification[sslcare_processing_alert]" value="1"' . checked( 1, $sslcare_processing_alert, false ) . '/>';
	    $html .= '<label for="sslcare_processing_alert">Enable this field for Order Processing Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_processing_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_processing_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_processing_template'] ) && $options['sslcare_processing_template'] != '' ) {
	        $sslcare_processing_template = $options['sslcare_processing_template'];
	    }
	    
	    $html = '<textarea id="sslcare_processing_template" rows="4" cols="98" name="sslcare_notification[sslcare_processing_template]" placeholder="">' . $sslcare_processing_template . '</textarea>';
	    $html .= '<br><label for="sslcare_processing_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_onhold_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_onhold_alert = get_option('sslcare_onhold_alert');
	    if( isset( $options['sslcare_onhold_alert'] ) && $options['sslcare_onhold_alert'] != '' ) {
	        $sslcare_onhold_alert = $options['sslcare_onhold_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_onhold_alert" name="sslcare_notification[sslcare_onhold_alert]" value="1"' . checked( 1, $sslcare_onhold_alert, false ) . '/>';
	    $html .= '<label for="sslcare_onhold_alert">Enable this field for Order On-Hold Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_onhold_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_onhold_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_onhold_template'] ) && $options['sslcare_onhold_template'] != '' ) {
	        $sslcare_onhold_template = $options['sslcare_onhold_template'];
	    }
	    
	    $html = '<textarea id="sslcare_onhold_template" rows="4" cols="98" name="sslcare_notification[sslcare_onhold_template]" placeholder="">' . $sslcare_onhold_template . '</textarea>';
	    $html .= '<br><label for="sslcare_onhold_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_failed_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_failed_alert = get_option('sslcare_failed_alert');
	    if( isset( $options['sslcare_failed_alert'] ) && $options['sslcare_failed_alert'] != '' ) {
	        $sslcare_failed_alert = $options['sslcare_failed_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_failed_alert" name="sslcare_notification[sslcare_failed_alert]" value="1"' . checked( 1, $sslcare_failed_alert, false ) . '/>';
	    $html .= '<label for="sslcare_failed_alert">Enable this field for Order Failed Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_failed_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_failed_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_failed_template'] ) && $options['sslcare_failed_template'] != '' ) {
	        $sslcare_failed_template = $options['sslcare_failed_template'];
	    }
	    
	    $html = '<textarea id="sslcare_failed_template" rows="4" cols="98" name="sslcare_notification[sslcare_failed_template]" placeholder="">' . $sslcare_failed_template . '</textarea>';
	    $html .= '<br><label for="sslcare_failed_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_canceled_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_canceled_alert = get_option('sslcare_canceled_alert');
	    if( isset( $options['sslcare_canceled_alert'] ) && $options['sslcare_canceled_alert'] != '' ) {
	        $sslcare_canceled_alert = $options['sslcare_canceled_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_canceled_alert" name="sslcare_notification[sslcare_canceled_alert]" value="1"' . checked( 1, $sslcare_canceled_alert, false ) . '/>';
	    $html .= '<label for="sslcare_canceled_alert">Enable this field for Order Canceled Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_canceled_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_canceled_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_canceled_template'] ) && $options['sslcare_canceled_template'] != '' ) {
	        $sslcare_canceled_template = $options['sslcare_canceled_template'];
	    }
	    
	    $html = '<textarea id="sslcare_canceled_template" rows="4" cols="98" name="sslcare_notification[sslcare_canceled_template]" placeholder="">' . $sslcare_canceled_template . '</textarea>';
	    $html .= '<br><label for="sslcare_canceled_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_completed_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_completed_alert = get_option('sslcare_completed_alert');
	    if( isset( $options['sslcare_completed_alert'] ) && $options['sslcare_completed_alert'] != '' ) {
	        $sslcare_completed_alert = $options['sslcare_completed_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_completed_alert" name="sslcare_notification[sslcare_completed_alert]" value="1"' . checked( 1, $sslcare_completed_alert, false ) . '/>';
	    $html .= '<label for="sslcare_completed_alert">Enable this field for Order Completed Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_completed_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_completed_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_completed_template'] ) && $options['sslcare_completed_template'] != '' ) {
	        $sslcare_completed_template = $options['sslcare_completed_template'];
	    }
	    
	    $html = '<textarea id="sslcare_completed_template" rows="4" cols="98" name="sslcare_notification[sslcare_completed_template]" placeholder="">' . $sslcare_completed_template . '</textarea>';
	    $html .= '<br><label for="sslcare_completed_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_refund_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_refund_alert = get_option('sslcare_refund_alert');
	    if( isset( $options['sslcare_refund_alert'] ) && $options['sslcare_refund_alert'] != '' ) {
	        $sslcare_refund_alert = $options['sslcare_refund_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_refund_alert" name="sslcare_notification[sslcare_refund_alert]" value="1"' . checked( 1, $sslcare_refund_alert, false ) . '/>';
	    $html .= '<label for="sslcare_refund_alert">Enable this field for Order Refund Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_refund_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_refund_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_refund_template'] ) && $options['sslcare_refund_template'] != '' ) {
	        $sslcare_refund_template = $options['sslcare_refund_template'];
	    }
	    
	    $html = '<textarea id="sslcare_refund_template" rows="4" cols="98" name="sslcare_notification[sslcare_refund_template]" placeholder="">' . $sslcare_refund_template . '</textarea>';
	    $html .= '<br><label for="sslcare_refund_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_partial_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_partial_alert = get_option('sslcare_partial_alert');
	    if( isset( $options['sslcare_partial_alert'] ) && $options['sslcare_partial_alert'] != '' ) {
	        $sslcare_partial_alert = $options['sslcare_partial_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_partial_alert" name="sslcare_notification[sslcare_partial_alert]" value="1"' . checked( 1, $sslcare_partial_alert, false ) . '/>';
	    $html .= '<label for="sslcare_partial_alert">Enable this field for Order Partially Paid Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_partial_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_partial_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_partial_template'] ) && $options['sslcare_partial_template'] != '' ) {
	        $sslcare_partial_template = $options['sslcare_partial_template'];
	    }
	    
	    $html = '<textarea id="sslcare_partial_template" rows="4" cols="98" name="sslcare_notification[sslcare_partial_template]" placeholder="">' . $sslcare_partial_template . '</textarea>';
	    $html .= '<br><label for="sslcare_partial_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_shipped_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_shipped_alert = get_option('sslcare_shipped_alert');
	    if( isset( $options['sslcare_shipped_alert'] ) && $options['sslcare_shipped_alert'] != '' ) {
	        $sslcare_shipped_alert = $options['sslcare_shipped_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_shipped_alert" name="sslcare_notification[sslcare_shipped_alert]" value="1"' . checked( 1, $sslcare_shipped_alert, false ) . '/>';
	    $html .= '<label for="sslcare_shipped_alert">Enable this field for Order Shipped Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_shipped_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_shipped_template = "Dear {{name}}, your order is {{status}}, Your total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_shipped_template'] ) && $options['sslcare_shipped_template'] != '' ) {
	        $sslcare_shipped_template = $options['sslcare_shipped_template'];
	    }
	    
	    $html = '<textarea id="sslcare_shipped_template" rows="4" cols="98" name="sslcare_notification[sslcare_shipped_template]" placeholder="">' . $sslcare_shipped_template . '</textarea>';
	    $html .= '<br><label for="sslcare_shipped_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function sslcare_otp_login_register_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_otp_login_register_template = "Your OTP is {{otp}}.\nThank You\n".get_bloginfo('name');
	    if( isset( $options['sslcare_otp_login_register_template'] ) && $options['sslcare_otp_login_register_template'] != '' ) {
	        $sslcare_otp_login_register_template = $options['sslcare_otp_login_register_template'];
	    }
	    
	    $html = '<textarea id="sslcare_otp_login_register_template" rows="4" cols="98" name="sslcare_notification[sslcare_otp_login_register_template]" placeholder="">' . $sslcare_otp_login_register_template . '</textarea>';
	    $html .= '<br><label for="sslcare_otp_login_register_template"><b>Variables : </b>{{otp}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	public function admin_sms_settings_callback() {
	    echo "<hr>";
	}

	public function sslcare_admin_sms_alert_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_admin_sms_alert = get_option('sslcare_admin_sms_alert');
	    if( isset( $options['sslcare_admin_sms_alert'] ) && $options['sslcare_admin_sms_alert'] != '' ) {
	        $sslcare_admin_sms_alert = $options['sslcare_admin_sms_alert'];
	    }

	    $html = '<input type="checkbox" id="sslcare_admin_sms_alert" name="sslcare_notification[sslcare_admin_sms_alert]" value="1"' . checked( 1, $sslcare_admin_sms_alert, false ) . '/>';
	    $html .= '<label for="sslcare_admin_sms_alert">Enable this field only for Admin SMS Alert</label>';
	    
	    echo $html;
	}

	public function sslcare_admin_phone_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_admin_phone = '';
	    if( isset( $options['sslcare_admin_phone'] ) && $options['sslcare_admin_phone'] != '' ) {
	        $sslcare_admin_phone = $options['sslcare_admin_phone'];
	    }

	    $html = '<input type="text" name="sslcare_notification[sslcare_admin_phone]" value="' . $sslcare_admin_phone . '" size="45" placeholder="Admin Phone Number"/>';

	    echo $html;
	}

	public function sslcare_admin_sms_template_callback() {
	    $options = get_option( 'sslcare_notification' );

	    $sslcare_admin_sms_template = "Order has been placed by {{name}}, order is {{status}}, total amount is {{amount}} {{currency}} for order id {{order_id}} order number {{order_number}}.\nThank You\n".get_bloginfo('name');

	    if( isset( $options['sslcare_admin_sms_template'] ) && $options['sslcare_admin_sms_template'] != '' ) {
	        $sslcare_admin_sms_template = $options['sslcare_admin_sms_template'];
	    }
	    
	    $html = '<textarea id="sslcare_admin_sms_template" rows="4" cols="98" name="sslcare_notification[sslcare_admin_sms_template]" placeholder="">' . $sslcare_admin_sms_template . '</textarea>';
	    $html .= '<br><label for="sslcare_admin_sms_template"><b>Variables : </b>{{name}}, {{status}}, {{amount}}, {{currency}}, {{order_id}}, {{order_number}}, {{payment_method}}</label>';
	    $html .= '<hr>';

	    echo $html;
	}

	############################################# Validate Fields ##############################################


	public function sslcare_sanitize_settings( $input ) {
	    
	    global $plugin_slug;
	    $output = array();

	    // Determine the selected platform
		$selected_platform = isset($input['sslcare_api_version']) ? $input['sslcare_api_version'] : '';

		// Enable Plugin
		if ( isset( $input['enable_plugin'] ) ) {
			$output['enable_plugin'] = $input['enable_plugin'];
		}

		// Platform Selection
		if ( isset( $input['sslcare_api_version'] ) ) {
			$output['sslcare_api_version'] = sanitize_textarea_field($input['sslcare_api_version']);
		} else {
			add_settings_error( 'sslcare_notification', 'platform-error', esc_html__( 'Please select API Platform.', $plugin_slug));
		}

		// Conditional validation based on platform selection

		// Validate API Hash Token only if ISMS Plus is selected
		if ($selected_platform === 'ismsplus') {
			if ( isset( $input['api_hash_token'] ) && $input['api_hash_token'] != "" ) {
				$output['api_hash_token'] = sanitize_textarea_field($input['api_hash_token']);
			} else {
				add_settings_error( 'sslcare_notification', 'token-error', esc_html__( 'Please enter valid Hash Token.', $plugin_slug));
			}
		} else {
			// Set empty value for API Hash Token if ISMS Plus is not selected
			$output['api_hash_token'] = '';
		}

		// Validate API Username and Password only if ISMS is selected
		if ($selected_platform === 'isms') {
			if ( isset( $input['api_username'] ) && $input['api_username'] != "" ) {
				$output['api_username'] = sanitize_textarea_field($input['api_username']);
			} else {
				add_settings_error( 'sslcare_notification', 'username-error', esc_html__( 'Please enter API Username.', $plugin_slug));
			}

			if ( isset( $input['api_password'] ) && $input['api_password'] != "" ) {
				$output['api_password'] = sanitize_textarea_field($input['api_password']);
			} else {
				add_settings_error( 'sslcare_notification', 'password-error', esc_html__( 'Please enter API Password.', $plugin_slug));
			}
		} else {
			// Set empty values for API Username and Password if ISMS is not selected
			$output['api_username'] = '';
			$output['api_password'] = '';
		}

		// Common fields (these will be validated regardless of platform)
		if ( isset( $input['api_url'] ) && $input['api_url'] != "" ) {
			$output['api_url'] = sanitize_textarea_field($input['api_url']);
		} else {
			add_settings_error( 'sslcare_notification', 'url-error', esc_html__( 'Please enter URL.', $plugin_slug));
		}

		if ( isset( $input['sslcare_api_sid'] ) && $input['sslcare_api_sid'] != "" ) {
			$output['sslcare_api_sid'] = sanitize_textarea_field($input['sslcare_api_sid']);
		} else {
			add_settings_error( 'sslcare_notification', 'sid-error', esc_html__( 'Please enter SID/Stakeholder.', $plugin_slug));
		}

		if ( isset( $input['enable_unicode_sms'] ) ) {
			$output['enable_unicode_sms'] = $input['enable_unicode_sms'];
		}

		// Additional alert and template fields
		if ( isset( $input['sslcare_pending_alert'] ) ) {
			$output['sslcare_pending_alert'] = $input['sslcare_pending_alert'];
		}

		if ( isset( $input['sslcare_pending_template'] ) && $input['sslcare_pending_template'] != "" ) {
			$output['sslcare_pending_template'] = sanitize_textarea_field($input['sslcare_pending_template']);
		}

		if ( isset( $input['sslcare_processing_alert'] ) ) {
			$output['sslcare_processing_alert'] = $input['sslcare_processing_alert'];
		}

		if ( isset( $input['sslcare_processing_template'] ) && $input['sslcare_processing_template'] != "" ) {
			$output['sslcare_processing_template'] = sanitize_textarea_field($input['sslcare_processing_template']);
		}

		if ( isset( $input['sslcare_onhold_alert'] ) ) {
			$output['sslcare_onhold_alert'] = $input['sslcare_onhold_alert'];
		}

		if ( isset( $input['sslcare_onhold_template'] ) && $input['sslcare_onhold_template'] != "" ) {
			$output['sslcare_onhold_template'] = sanitize_textarea_field($input['sslcare_onhold_template']);
		}

		if ( isset( $input['sslcare_failed_alert'] ) ) {
			$output['sslcare_failed_alert'] = $input['sslcare_failed_alert'];
		}

		if ( isset( $input['sslcare_failed_template'] ) && $input['sslcare_failed_template'] != "" ) {
			$output['sslcare_failed_template'] = sanitize_textarea_field($input['sslcare_failed_template']);
		}

		if ( isset( $input['sslcare_canceled_alert'] ) ) {
			$output['sslcare_canceled_alert'] = $input['sslcare_canceled_alert'];
		}

		if ( isset( $input['sslcare_canceled_template'] ) && $input['sslcare_canceled_template'] != "" ) {
			$output['sslcare_canceled_template'] = sanitize_textarea_field($input['sslcare_canceled_template']);
		}

		if ( isset( $input['sslcare_completed_alert'] ) ) {
			$output['sslcare_completed_alert'] = $input['sslcare_completed_alert'];
		}

		if ( isset( $input['sslcare_completed_template'] ) && $input['sslcare_completed_template'] != "" ) {
			$output['sslcare_completed_template'] = sanitize_textarea_field($input['sslcare_completed_template']);
		}

		if ( isset( $input['sslcare_refund_alert'] ) ) {
			$output['sslcare_refund_alert'] = $input['sslcare_refund_alert'];
		}

		if ( isset( $input['sslcare_refund_template'] ) && $input['sslcare_refund_template'] != "" ) {
			$output['sslcare_refund_template'] = sanitize_textarea_field($input['sslcare_refund_template']);
		}

		if ( isset( $input['sslcare_partial_alert'] ) ) {
			$output['sslcare_partial_alert'] = $input['sslcare_partial_alert'];
		}

		if ( isset( $input['sslcare_partial_template'] ) && $input['sslcare_partial_template'] != "" ) {
			$output['sslcare_partial_template'] = sanitize_textarea_field($input['sslcare_partial_template']);
		}

		if ( isset( $input['sslcare_shipped_alert'] ) ) {
			$output['sslcare_shipped_alert'] = $input['sslcare_shipped_alert'];
		}

		if ( isset( $input['sslcare_shipped_template'] ) && $input['sslcare_shipped_template'] != "" ) {
			$output['sslcare_shipped_template'] = sanitize_textarea_field($input['sslcare_shipped_template']);
		}

		if ( isset( $input['sslcare_otp_login_register_template'] ) && $input['sslcare_otp_login_register_template'] != "" ) {
			$output['sslcare_otp_login_register_template'] = sanitize_textarea_field($input['sslcare_otp_login_register_template']);
		}

		if ( isset( $input['sslcare_admin_sms_alert'] ) ) {
			$output['sslcare_admin_sms_alert'] = $input['sslcare_admin_sms_alert'];
		}

		if ( isset( $input['sslcare_admin_phone'] ) && $input['sslcare_admin_phone'] != "" ) {
			$output['sslcare_admin_phone'] = sanitize_textarea_field($input['sslcare_admin_phone']);
		}

		if ( isset( $input['sslcare_admin_sms_template'] ) && $input['sslcare_admin_sms_template'] != "" ) {
			$output['sslcare_admin_sms_template'] = sanitize_textarea_field($input['sslcare_admin_sms_template']);
		}

		return apply_filters( 'sslcare_notification', $output, $input );
	}
}