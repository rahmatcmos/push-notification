<?php

namespace DeveloperDynamo\PushNotification\Services\GCM;

use Config;
use DeveloperDynamo\PushNotification\Services\ServiceInterface;
use DeveloperDynamo\PushNotification\Payload\AbstractPayload;

class AndroidService implements ServiceInterface
{
	/**
	 * Name of platform
	 * 
	 * @var string
	 */
	protected $platform = 'android';
	
	/**
	 * GCM server endpoint
	 * 
	 * @var string
	 */
	protected $uri = 'https://android.googleapis.com/gcm/send';

	/**
	 * Send notification to devices tokens
	 * 
	 * @throws InvalidArgumentException
	 * @param AbstractPayload $payload
	 * @param array $tokens
	 */
	public function send($payload, $tokens)
	{
		if (!($payload instanceof AbstractPayload)) {
			throw new \InvalidArgumentException('Payload must be an instance of AbstractPayload');
		}
		
		if(!is_array($tokens)){
			throw new \InvalidArgumentException('Tokens must be an array');
		}
		
    	if(!count($tokens)>0){
    		return true;
    	}
    	
		$gcm_message = [
				"registration_ids" => $tokens,
				"data" => $payload->getAndroidFormat(),
		];
		
		$headers = [
				"Authorization: key=".Config::get('pushnotification.android.apiKey'),
				"Content-Type: application/json"
		];
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL, $this->uri);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($gcm_message));
		$result=curl_exec($ch);
		
		if($result === false){
			throw new \Exception("Curl failed: ".curl_error($ch));
		}
		
		curl_close($ch);
		
		return $result;
	}
	/**
	 * Accessor for platform name
	 * 
	 * @return string
	 */
	public function getPlatformName() 
	{
		return $this->platform;
	}
}
?>