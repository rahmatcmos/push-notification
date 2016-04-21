<?php

namespace DeveloperDynamo\PushNotification\Services\APNS;

use DeveloperDynamo\PushNotification\Services\ServiceInterface;
use DeveloperDynamo\PushNotification\Payload\AbstractPayload;

class ApnService extends AbstractClient implements ServiceInterface
{
	/**
	 * Name of platform
	 * 
	 * @var string
	 */
	protected $platform = 'ios';
	
	/**
	 * APN service URI
	 * 
	 * @var string
	 */
	protected $uri = 'ssl://gateway.push.apple.com:2195';

	/**
	 * Send notification to devices tokens
	 *
	 * @throws InvalidArgumentException
	 * @param AbstractPayload $payload
	 * @param array $tokens
	 */
	public function send($payload, $tokens){
    	if(!count($tokens)>0)
    		return true;

		// Encode payload as JSON
		$json_payload = json_encode($payload->getIosFormat());
		
		// Build the binary notification to each token
		$data = '';
		foreach($tokens as $tk){
			$data .= chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $tk)) . pack('n', strlen($json_payload)) . $json_payload;
		}
		
		// Send data to the server
		$this->connect();
		return $this->write($data);
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