<?php 
	#-----------------
	# Api requesting script.
	#-----------------
namespace Sslcare\Sms\Api;

class Sslcare_Sms_Api
{
	############################### Process parameters for GET request ################################

	public static function set_get_parameter($phone_number, $sms_text)
	{
		$unique_id = uniqid();
		$settings = get_option( 'sslcare_notification' );

    	$sslcare_api_sid 	 = $settings['sslcare_api_sid'];
    	$sslcare_api_version = $settings['sslcare_api_version'];
		
		if($sslcare_api_version == "isms" && $settings['api_username'] != "" && $settings['api_password'] != "" && $sslcare_api_sid != "")
		{
			$api_username 	= $settings['api_username'];
    		$api_password 	= $settings['api_password'];
			
			if(isset($settings['enable_unicode_sms']) && $settings['enable_unicode_sms'] != "")
			{
				$sms = self::convertBanglatoUnicode($sms_text);
				$param = "user=$api_username&pass=$api_password&sid=$sslcare_api_sid&sms=$sms&msisdn=$phone_number&csmsid=$unique_id";
			}
			else
			{
				$sms = urlencode($sms_text);
				$param = "user=$api_username&pass=$api_password&sid=$sslcare_api_sid&sms=$sms&msisdn=$phone_number&csmsid=$unique_id";
			}

			return $param;
		}
		else if($sslcare_api_version == "ismsplus" && $settings['api_hash_token'] != "" && $sslcare_api_sid != "")
		{
			$sslcare_api_token = $settings['api_hash_token'];
			$param = [
		        "api_token" => $sslcare_api_token,
		        "sid" => $sslcare_api_sid,
		        "msisdn" => $phone_number,
		        "sms" => $sms_text,
		        "csms_id" => $unique_id
		    ];
		    $param = json_encode($param);
				
			return $param;
		}
		else{
			return "404";
		}
	}


	################################# Process API For GET REQUEST ##################################

	public static function call_to_get_api($peram)
	{
		$settings 		= get_option( 'sslcare_notification' );
		$sslcare_api 	= $settings['sslcare_api_version'];

		if($sslcare_api == "isms" && $settings['api_username'] != "" && $settings['api_password'] != "")
		{
			$api_url 		= "http://sms.sslwireless.com/pushapi/dynamic/server.php";
			$url = $api_url."?".$peram;

			$response = wp_remote_post(
				$url,
				array(
					'method'      => 'GET',
					'timeout'     => 30,
					'redirection' => 10,
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(),
					'body'        => array(),
					'cookies'     => array(),
				)
			);
		}
		else if($sslcare_api == "ismsplus" && $settings['api_hash_token'] != "")
		{
			// Live URL
			// $api_url 		= "https://smsplus.sslwireless.com/api/v3/send-sms";
			// Test URL
			// $api_url 		= "http://ismsapi.publicdemo.xyz/api/v3/send-sms";
			if(isset($settings['api_url']) && !empty($settings['api_url'])){
				$api_url = $settings['api_url'];
			}else{
				$api_url = "https://smsplus.sslwireless.com/api/v3/send-sms";
			}
			

			$headers = array( 
				'Content-type' => 'application/json',
				'Content-length' => strlen($peram),
				'accept' => 'application/json'
			);

			$response = wp_remote_post(
				$api_url,
				array(
					'method'      => 'POST',
					'timeout'     => 30,
					'redirection' => 10,
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => $headers,
					'body'        => $peram,
					'cookies'     => array(),
				)
			);
		}
		

		if ( is_wp_error( $response ) ) 
		{
		   	$apiresponse = $response->get_error_message();
		} 
		else 
		{
		   	$apiresponse = array($response['response'], $response['body']);
		}

		return $apiresponse;
	}

	public static function convertBanglatoUnicode($BanglaText)
	{
		$unicodeBanglaTextForSms = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));
		return $unicodeBanglaTextForSms;
	}

}