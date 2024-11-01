<?php
#-------------------------
# Register & Trigger Hook For Woocommerce alert
#-------------------------

namespace Sslcare\Sms\Woosms;

require_once( SSLW_SMS_PATH . 'lib/sslcare-sms-api.php' );
use Sslcare\Sms\Api\Sslcare_Sms_Api;

global $sslcare_settings;
global $woocommerce;

class Sslcare_Woo_Alert
{
	public function __construct()
	{
		$sslcare_settings = get_option( 'sslcare_notification' );

		if(isset($sslcare_settings['sslcare_pending_alert']) && $sslcare_settings['sslcare_pending_alert'] != "")
		{
			add_action( 'woocommerce_order_status_pending', array($this, 'sslcare_alert_pending'));
		}
		if(isset($sslcare_settings['sslcare_processing_alert']) && $sslcare_settings['sslcare_processing_alert'] != "")
		{
			add_action( 'woocommerce_order_status_processing', array($this, 'sslcare_alert_processing'));
		}
		if(isset($sslcare_settings['sslcare_onhold_alert']) && $sslcare_settings['sslcare_onhold_alert'] != "")
		{
			add_action( 'woocommerce_order_status_on-hold', array($this, 'sslcare_alert_hold'));
		}
		if(isset($sslcare_settings['sslcare_failed_alert']) && $sslcare_settings['sslcare_failed_alert'] != "")
		{
			add_action( 'woocommerce_order_status_failed', array($this, 'sslcare_alert_failed'));
		}
		if(isset($sslcare_settings['sslcare_canceled_alert']) && $sslcare_settings['sslcare_canceled_alert'] != "")
		{
			add_action( 'woocommerce_order_status_cancelled', array($this, 'sslcare_alert_cancelled'));
		}
		if(isset($sslcare_settings['sslcare_completed_alert']) && $sslcare_settings['sslcare_completed_alert'] != "")
		{
			add_action( 'woocommerce_order_status_completed', array($this, 'sslcare_alert_completed'));
		}
		if(isset($sslcare_settings['sslcare_refund_alert']) && $sslcare_settings['sslcare_refund_alert'] != "")
		{
			add_action( 'woocommerce_order_status_refunded', array($this, 'sslcare_alert_refunded'));
		}
		if(isset($sslcare_settings['sslcare_partial_alert']) && $sslcare_settings['sslcare_partial_alert'] != "")
		{
			add_action('woocommerce_order_status_partially-paid', array($this, 'sslcare_alert_partially'));
		}
		if(isset($sslcare_settings['sslcare_shipped_alert']) && $sslcare_settings['sslcare_shipped_alert'] != "")
		{
			add_action( 'woocommerce_order_status_shipped', array($this, 'sslcare_alert_shipped'));
		}
	}

	public function sslcare_alert_pending($order_id) {

		global $wpdb;
	    global $woocommerce;
		$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Pending';
	    $smstext = trim($sslcare_settings['sslcare_pending_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_pending_alert']))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }
            
            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

    public function sslcare_alert_failed($order_id) {

    	global $wpdb;
	    global $woocommerce;
    	$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Failed';
	    $smstext = trim($sslcare_settings['sslcare_failed_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_failed_alert'] ))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

    public function sslcare_alert_hold($order_id) {

    	global $wpdb;
	    global $woocommerce;
    	$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'On-Hold';
	    $smstext = trim($sslcare_settings['sslcare_onhold_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_onhold_alert']))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

    public function sslcare_alert_processing($order_id) {
 
    	global $wpdb;
    	global $woocommerce;
    	$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();
	    

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Processing';
	    $smstext = trim($sslcare_settings['sslcare_processing_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_processing_alert'] ))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }
            if(isset($sslcare_settings['sslcare_admin_sms_alert']) && $sslcare_settings['sslcare_admin_phone'] != "" && $sslcare_settings['sslcare_admin_sms_template'] != "")
            {
            	$adminsmstext = $sslcare_settings['sslcare_admin_sms_template'];
            	$adminphone = $sslcare_settings['sslcare_admin_phone'];

            	$adminsmstext = str_ireplace("{{name}}", $name, $adminsmstext);
		    	$adminsmstext = str_ireplace("{{status}}", $status, $adminsmstext);
		    	$adminsmstext = str_ireplace("{{amount}}", $order_amount, $adminsmstext);
		    	$adminsmstext = str_ireplace("{{currency}}", $currency, $adminsmstext);
		        $adminsmstext = str_ireplace("{{order_id}}", $order_id, $adminsmstext);
				$adminsmstext = str_ireplace("{{order_number}}", $order_number, $adminsmstext);
				$adminsmstext = str_ireplace("{{payment_method}}", $payment_method, $adminsmstext);
            	$adminresponse = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($adminphone, $adminsmstext));
            	$this->save_response(get_bloginfo('name'), $adminphone, 'Admin Notification', $adminresponse);
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

    public function sslcare_alert_completed($order_id) {

    	global $wpdb;
	    global $woocommerce;
    	$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Completed';
	    $smstext = trim($sslcare_settings['sslcare_completed_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_completed_alert'] ))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

    public function sslcare_alert_refunded($order_id) {

    	global $wpdb;
	    global $woocommerce;
    	$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Refunded';
	    $smstext = trim($sslcare_settings['sslcare_refund_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_refund_alert']))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

    public function sslcare_alert_cancelled($order_id) {

    	global $wpdb;
	    global $woocommerce;
    	$sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Cancelled';
	    $smstext = trim($sslcare_settings['sslcare_canceled_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_canceled_alert']))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
    }

	public function sslcare_alert_shipped($order_id){

		global $wpdb;
	    global $woocommerce;
	    $sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Shipped';
	    $smstext = trim($sslcare_settings['sslcare_shipped_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_shipped_alert']))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
	}

	public function sslcare_alert_partially($order_id){

		global $wpdb;
	    global $woocommerce;
	    $sslcare_settings = get_option( 'sslcare_notification' );
	    $order 			= wc_get_order( $order_id );
		$order_number   = $order->get_order_number();
	    $order_amount	= $order->get_total();
	    $user 			= $order->get_user();
	    $user_id 		= $order->get_user_id();
	    $currency 		= $order->get_currency();
		$payment_method = $order->get_payment_method_title();

	    if($order->get_billing_phone() != "")
	    {
	    	$name    		 = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	    	$customer_mobile = $order->get_billing_phone();
	    }
	    

	    $status  = 'Partially Paid';
	    $smstext = trim($sslcare_settings['sslcare_partial_template']);
	    $sms_type = 'Order Notification Alert - '.$status;

	    if( isset($sslcare_settings['enable_plugin']) && !empty($customer_mobile) && !empty($smstext) && isset($sslcare_settings['sslcare_partial_alert']))
	    {
	    	$smstext = str_ireplace("{{name}}", $name, $smstext);
	    	$smstext = str_ireplace("{{status}}", $status, $smstext);
	    	$smstext = str_ireplace("{{amount}}", $order_amount, $smstext);
	    	$smstext = str_ireplace("{{currency}}", $currency, $smstext);
	        $smstext = str_ireplace("{{order_id}}", $order_id, $smstext);
			$smstext = str_ireplace("{{order_number}}", $order_number, $smstext);
			$smstext = str_ireplace("{{payment_method}}", $payment_method, $smstext);
	        
	        if($smstext != "")
            {
                $response = Sslcare_Sms_Api::call_to_get_api(Sslcare_Sms_Api::set_get_parameter($customer_mobile, $smstext));
            }

            $this->save_response($name, $customer_mobile, $sms_type, $response);
	    }
	}

	public function save_response($name, $customer_mobile, $sms_type, $response)
	{
		if($response != "")
		{
			global $wpdb;
			$table_woo_name = $wpdb->prefix . "sslcare_woo_alert";
			$wpdb->insert(
	            $table_woo_name,
	            array(
	                'customer_name' => sanitize_text_field($name),
	                'phone_no' => $customer_mobile,
	                'sms_type' => sanitize_text_field($sms_type),
	                'api_response' => sanitize_text_field(serialize($response))
	            )
	        );
		}
	}

}