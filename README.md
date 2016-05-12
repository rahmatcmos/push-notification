# push-notification
For complete introduction see this article: [Developer Dynamo](http://developerdynamo.it/2016/05/01/super-powerfull-laravel-pushnotification-package/)

With Developer Dynamo Push Notification you can send millions messages to millions devices with one line of code.

This package exploit the best Laravel features to execute very expensive tasks in terms of system resources as sending massive push notification messages.

All of this features are masked to you that you can use always only one line of code.

###Queue support
With Queue you can delegate sending task to an external Queue service as Amazon SQS to drastically increase your application performace without any change of your codebase.

This is not the same thing to delegate push notification management, but delegate only tasks execution, eg. sending Email, execute job, and now send push notification.

Furthermore sending notifications for iOS devices have a specific treatment concerning behavior of Apple APN service. It’s also available an interface for Apple Feedback server to verify which tokens are invalid in your DB and remove them from your archive to prevent failed sending.

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
    protected $table = "push_tokens_table";
}
```

To fit your model to be used directly from PushNotification Package you simply need to attach our Trait:
```
use DeveloperDynamo\PushNotification\TokenTrait;

class YourPushTokenTable extends Model
{
    use TokenTrait;

    protected $table = 'push_tokens_table';
}
```

To works automatically with PushNotification Package your table needs two columns, one that contain platform name, and the other one that contain device token.

By default this two columns name are cosidered `"platform"` and `"device_token"`, but if your table on DB used different names you can customize them.

For example, in your table you have platform column named "os", and column to store device_token is named "token". You can override standard name used from package using `$columnName` property:

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

#Send push notification

###Regular sending
```
//Eloquent model with TokenTrait
$tokens = YourPushTokenTable::all();

//send directly
//$tokens needs to be an array
NotificationBridge::send(AbstractPayload $payload, $tokens);
```

###Queue push sending 
You can queue your push notification sending to improve your system performace

```
//Eloquent model with TokenTrait
$tokens = YourPushTokenTable::all();

//push in queue
//$tokens needs to be an array
NotificationBridge::queue(AbstractPayload $payload, $tokens, "queue-name");
```

With latest parameter you can schedule job in a specific queue. 

