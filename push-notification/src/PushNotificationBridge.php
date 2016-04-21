<?php

namespace App\PushNotification;

use App\PushNotification\Payload\AbstractPayload;
use App\PushNotification\Driver\DriverInterface;

class PushNotificationBridge
{
	/**
	 * List of driver available
	 * 
	 * @var array
	 */
	protected $drivers = [
			Services\GCM\Driver::class,
			Services\APNS\ApnService::class,
	];
	
	/**
	 * Send notification to devices tokens
	 * 
	 * @throws \Exception
	 * @param AbstractPayload $payload
	 * @param array $tokens is an array [platform:'xxx', registration_id:'xxxxxx']
	 */
	public function send(AbstractPayload $payload, array $tokens)
	{
		/*
		 * Retrieve drivers and cast to DriverInterface
		 */
		foreach($this->drivers as $driver){
			/*
			 * Create driver instance
			 */
			$instance = new $driver();

			/*
			 * Check instance type
			 */
			if(! $instance instanceof DriverInterface){
				throw new \Exception("Driver must be a DriverInterface implementation");
			}
			
			/*
			 * Retrieve tokens for specific driver's platform
			 */
			$platform_tokens = $this->dispatchDeviceToken($instance->getPlatformName(), $tokens);
			
			/*
			 * Send payload to tokens across driver's platform
			 */
			$instance->send($payload, $platform_tokens);
		}
	}
	
	/**
	 * Dispatch tokens in the specific list for passed platform
	 * 
	 * @param string $platform
	 * @param array $tokens is an array [platform:'xxx', registration_id:'xxxxxx']
	 */
	protected function dispatchDeviceToken($platform, $tokens)
	{
		$platform_tokens = [];
			
		foreach($tokens as $tk){
			//If tokens is array<Model>
			if(!$tk instanceof Token)
				continue;
			
			//filtering tokens
			if($tk->platform === $platform){
				$platform_tokens[] = $tk->deviceId;
			}
		}
		
		return $platform_tokens;
	}
}