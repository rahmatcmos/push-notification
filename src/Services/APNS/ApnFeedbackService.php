<?php

namespace DeveloperDynamo\PushNotification\Services\APNS;

class APNSFeedbackDriver extends AbstractClient
{
	/**
	 * APNS feedback service URI
	 * 
	 * @var string
	 */
	protected $uri = 'ssl://feedback.push.apple.com:2196';

	/**
	 * Get Feedback
	 *
	 * @return array
	 */
	public function feedback()
	{
		if (!$this->isConnected()) {
			throw new Exception('You must first open the connection by calling connect()');
		}
		
		/*
		 * Read from socket
		 */
		$tokens = [];
		while ($token = $this->read(38)) {
			$tokens[] = new Feedback($token);
		}
		
		return $tokens;
	}
}