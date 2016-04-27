<?php

namespace DeveloperDynamo\PushNotification;

use DeveloperDynamo\PushNotification\Payload\AbstractPayload;
use DeveloperDynamo\PushNotification\Services\ServiceInterface;

class PushNotificationBridge
{
	/**
	 * List of driver available
	 * 
	 * @var array
	 */
	private $services = [
			Services\GCM\AndroidService::class,
			Services\APNS\ApnService::class,
	];
	
	/**
	 * Instance of queue connection
	 * 
	 * @var Queue
	 */
	protected $queue;
	
	/**
	 * Instance of laravel App
	 * 
	 * @var App
	 */
	protected $app;
	
	/**
	 * Create a new Mailer instance.
	 * 
	 * @param AbstractPayload $payload
	 * @param array $tokens
	 * @param string $queue
	 */
	public function __construct($app)
	{
		$this->app = $app;
		$this->setBridgeDependencies();
	}
	
	/**
	 * Queue a new push notification for sending.
	 * 
	 * @param AbstractPayload $payload
	 * @param array $tokens
	 * @param string $queue
	 */
	public function queue(AbstractPayload $payload, array $tokens, $queue = null)
	{
		return $this->queue->push('bridge@handleQueuedSending', compact($payload, $tokens), $queue);
	}
	
	/**
	 * Handle a queued push notification message job.
	 * 
	 * @param \Illuminate\Contracts\Queue\Job  $job
	 * @param DeveloperDynamo\PushNotification\Payload\AbstractPayload $payload
	 * @param array<DeveloperDynamo\PushNotification\Token> $tokens
     * @return void
	 */
	public function handleQueuedSending($job, $data)
	{
		$this->send($data['payload'], $data['tokens']);
		
		$job->delete();
	}
	
	/**
	 * Send notification to devices tokens
	 * 
	 * @throws \Exception
	 * @param AbstractPayload $payload
	 * @param array<Token> $tokens
	 */
	public function send(AbstractPayload $payload, array $tokens)
	{
		/*
		 * Retrieve drivers and cast to DriverInterface
		 */
		foreach($this->services as $service){
			/*
			 * Create driver instance
			 */
			$instance = new $service();

			/*
			 * Check instance type
			 */
			if(! $instance instanceof ServiceInterface){
				throw new \InvalidArgumentException("Driver must be a DriverInterface implementation");
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
	
	/**
	 * Set a few dependencies on the bridge instance.
	 * 
     * @return void
	 */
	protected function setBridgeDependencies() 
	{
		//if you want to see if a class is bound to the container you can use BOUND method
		if ($this->app->bound('queue')) {
			$this->queue = $this->app['queue.connection'];
		}
	}
}