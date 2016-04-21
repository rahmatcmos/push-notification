<?php

namespace DeveloperDynamo\PushNotification\Listeners;

use Carbon\Carbon;
use DB;
use App\PushNotification\PushNotificationBridge;
use App\PushNotification\Payload\AbstractPayload;

abstract class AbstractNotificationListener
{
	/**
	 * Send notification to all implemented platform
	 * 
	 * @param Payload $payload
	 * @param array<Token> $tokens
	 */
	public function send(AbstractPayload $payload, array $tokens)
	{
		//Send payload to all tokens across all implemented platform
    	$bridge = new PushNotificationBridge();
		$bridge->send($payload, $tokens);
		
		//Store notification on DB
		$this->store($payload, $tokens);
	}
	
	/**
	 * Store notification on DB for each user associated to the token
	 * 
	 * @param Payload $payload
	 */
	public function store($payload, $tokens)
	{
		$rows = [];
		foreach($tokens as $tk){
			$rows[] = [
					'title' => addslashes($payload->title),
					'content' => addslashes($payload->content),
					'state' => $payload->state,
					'params' => $payload->params,
					'img' => $payload->img,
					'account' => $tk->id,
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
			];
		}
		DB::table('notification')->insert($rows);
	}
}