<?php

namespace DeveloperDynamo\PushNotification;

class Token
{
	public $id;
	
	public $platform;
	
	public $deviceId;

	public function __construct($id, $platform, $deviceId)
	{
		$this->id = $id;
		$this->platform = $platform;
		$this->deviceId = $deviceId;
	}
	
	public function toArray()
	{
		return [
				'id' => $this->id,
				'platform' => $this->platform,
				'deviceId' => $this->deviceId,
		];
	}
}