<?php

namespace DeveloperDynamo\PushNotification\Services;

interface ServiceInterface
{	
	public function getPlatformName();
	public function send($payload, $tokens);
}