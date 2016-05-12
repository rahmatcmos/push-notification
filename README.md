# push-notification
For introduction see this article: [Developer Dynamo PushNotification package](http://developerdynamo.it/2016/05/01/super-powerfull-laravel-pushnotification-package/)

With Developer Dynamo Push Notification you can send millions messages to millions devices with one line of code.

This package use the best Laravel features to execute very expensive tasks in terms of system resources as sending massive push notifications messages.

You can use directly your Eloquent model to filter your tokens list and send a payload.

###Queue support
With Queue you can delegate sending task to an external Queue service as Amazon SQS to drastically increase your application performace without any change of your codebase.

###Platform actually supported
- iOS - apns (Apple Push Notificatio Service)
- android - GCM (Google Cloud Messaging)

#Install
Add follow line into "require" section in your composer.json:

```
"developerdynamo/push-notification": "1.*"
```

Update composer with command:

```
"composer update"
```

###Provider and Facade
Like all providers, put this follow lines in your config/app.php

```
'providers' => [
	...
	DeveloperDynamo\PushNotification\PushNotificationProvider::class,
],
```

```
'aliases' => [
	...
	'NotificationBridge' => DeveloperDynamo\PushNotification\Facades\PushNotificationBridge::class,
],
```

###Configuration
Finally you need to generate a configuration file for this package.
Run follow composer command:

```
php artisan vendor:publish --provider="DeveloperDynamo\PushNotification\PushNotificationProvider"
```

#Quick start
You should have a model to store devices informations into your database, for example: 
```
class YourPushTokenTable extends Model
{
    //
}
```

To fit your model to be used directly from PushNotification Package you simply need to attach our Trait:
```
use DeveloperDynamo\PushNotification\TokenTrait;

class YourPushTokenTable extends Model
{
    use TokenTrait;
}
```

To works automatically with PushNotification Package your table needs two columns, one that contains platform name, and the other one that contains device token.

By default this two columns name are cosidered `"platform"` and `"device_token"`, but if your table on DB use different names you can customize them according with your table's structure.

For example, in your table you have platform column named "os", and column to store device_token is named "token". You can overwrite standard name used from package using `$columnName` property:

```
class YourPushTokenTable extends Model
{
    use TokenTrait;
    
    /**
	 * Column name into DB table to store device information
	 * 
	 * @var array
	 */
	protected $columnName = [
			"platform" => "os",
			"device_token" => "token",
	];
}
```

In this way you can retrieve list of tokens directly from your DB table with Eloquent benefits and send your payload across all platforms without any other intermediate steps.

#Create your payload
You just create a class for each event's payload and implement `DeveloperDynamo\PushNotification\Contracts\Payload`.

```
namespace App\Payloads;

use App\Post;
use DeveloperDynamo\PushNotification\Contracts\Payload;

class InsertPostPayload extends Payload
{
	/**
	 * Generate Notification Payload for Add Post event
	 *
	 * @param Post $post
	 * @return void
	 */
	public function __construct(Post $post)
	{
		//IOS payload format	
		$this->iosPayload = [
				"alert" => [
					"title" => $post->title,
					"body" 	=> $post->content,
				],
		];
		
		//Android payload format
		$this->androidPayload = [
				"title" 	=> $post->title,
				"message" 	=> $post->content,
		];
	}
}
```


#Send example
Ok, now you can get lists of devices tokens from your DB and you can create a payload for specifics events notifications.
To sending payload to list of devices tokens you can use `NotificationBridge`

###Regular sending
```
$payload = new InsertPostPayload(Post::find($id));

//Eloquent model that use TokenTrait
$tokens = YourPushTokenTable::all();

//send directly
//$tokens needs to be an array
NotificationBridge::send($payload, $tokens);
```

###Queue push sending 
You can use queue to sending push notifications to improve your system performace

```
$payload = new InsertPostPayload(Post::find($id));

//Eloquent model that use TokenTrait
$tokens = YourPushTokenTable::all();

//push in queue
//$tokens needs to be an array
NotificationBridge::queue($payload, $tokens, "queue-name");
```

With latest parameter you can schedule job in a specific queue. 

