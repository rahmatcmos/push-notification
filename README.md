# push-notification
For introduction see this article: [Developer Dynamo PushNotification package](http://developerdynamo.it/2016/05/01/super-powerfull-laravel-pushnotification-package/)

There are more library in any language that help you to send push notifications but in my opinion nobody help you to make and manage easily a complete push notification management service.

With this package you can create your Payload collection for any relevant event that could be happen for your users, filter devices list using directly your Eloquent model and send from one to millions messages with a single line of code.

###Platform actually supported
- iOS - apns (Apple Push Notification Service)
- android - GCM (Google Cloud Messaging)

#Install
Add follow line into "require" section in your composer.json:

```json
"developerdynamo/push-notification": "1.*"
```

Update composer with command:

```json
"composer update"
```

###Provider and Facade
Like all providers, put this follow lines in your config/app.php

```php
'providers' => [
	...
	DeveloperDynamo\PushNotification\PushNotificationProvider::class,
],
```

```php
'aliases' => [
	...
	'NotificationBridge' => DeveloperDynamo\PushNotification\Facades\PushNotificationBridge::class,
],
```

###Configuration
Finally you need to generate a configuration file for this package.
Run follow composer command:

```php
php artisan vendor:publish --provider="DeveloperDynamo\PushNotification\PushNotificationProvider"
```
Remember to add your GCM api key and PEM certificate path in config file.

#Tokens
You should have a model to store devices informations into your database, for example: 
```php
class YourPushTokenTable extends Model
{
    //
}
```

To fit your model to be used directly from PushNotification Package you simply need to attach our Trait:

```php
use DeveloperDynamo\PushNotification\TokenTrait;

class YourPushTokenTable extends Model
{
    use TokenTrait;
}
```

To works automatically with PushNotification Package your table needs two columns, one that contains platform name, and the other one that contains device token.

By default this two columns names are cosidered `"platform"` and `"device_token"`, but if your table on DB use different names you can customize them according with your table's structure.

For example, in your table you have platform column named "os", and column to store device_token is named "token". You can overwrite standard name used from package using `$columnName` property:

```php
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

You can retrieve list of tokens directly from your DB table with Eloquent benefits and send your payload across all platforms without any other intermediate steps.

#Payload
You just create a class that implement `DeveloperDynamo\PushNotification\Contracts\Payload` and overwrite `iosPayload` and `androidPayload` properties with your payload content.

```php
namespace App\Payload;

use App\User;
use DeveloperDynamo\PushNotification\Contracts\Payload;

class AddPhotoPayload extends Payload
{
	/**
	 * Generate Notification Payload
	 *
	 * @param User $user
	 * @return void
	 */
	public function __construct(User user)
	{
		//IOS payload format	
		$this->iosPayload = [
				"alert" => [
					"title" => $user->first_name." posted a photo",
					"body" 	=> $user->first_name." added a new photo in her gallery",
				],
		];
		
		//Android payload format
		$this->androidPayload = [
				"title" 	=> $user->first_name." ha postato una foto",
				"message" 	=> $user->first_name." added a new photo in her gallery",
		];
	}
}
```
You can go on to create your payload collections for every event or message that you want send to your users.

#Send
Ok, now you are able to get a list of devices tokens from your DB and you have a payload for your specific event.
To sending payload to list of devices you can use `NotificationBridge`.

###Regular sending
```php
//Create payload
$payload = new InsertPostPayload(User::findOrFail(1));

//Retrieve devices list with your own criteria
$tokens = YourPushTokenTable::all();

//send directly
NotificationBridge::send($payload, $tokens);
```

###Queue push sending 
You can use queue to sending push notifications to improve your system performace

```php
//Create payload
$payload = new InsertPostPayload(User::findOrFail(1));

//Retrieve devices list with your own criteria
$tokens = YourPushTokenTable::all();

//push in queue
NotificationBridge::queue($payload, $tokens, "queue-name");
```

With latest parameter you can schedule job in a specific queue. 

