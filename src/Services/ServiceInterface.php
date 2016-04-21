<?php

namespace DeveloperDynamo\PushNotification\Services;

interface DriverInterface
{	
	public function getPlatformName();
	public function send($payload, $tokens);
}