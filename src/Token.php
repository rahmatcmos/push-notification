<?php

namespace DeveloperDynamo\PushNotification;

class Token
{
	public $platform;
	
	public $deviceId;

	public function __construct($platform, $deviceId)
	{
		$this->platform = $platform;
		$this->deviceId = $deviceId;
	}
	
	public function toArray()
	{
		return [
				'platform' => $this->platform,
				'deviceId' => $this->deviceId,
		];
	}
}