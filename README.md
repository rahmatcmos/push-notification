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

#Configure Laravel 5
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

#Publish configuration
Finally you need to generate a configuration file for this package.
Run follow composer command:

```
php artisan vendor:publish --provider="DeveloperDynamo\PushNotification\PushNotificationProvider"
```

#Send push notification

###Regular sending
```
//Using Eloquent
$list = YourPushTokenTable::all();
 
//Create list for NotificationBridge
$bridgeTokens = [];
foreach ($list as $item){
    $bridgeTokens[] = new Token($item->platform, $item->device_token);
}

//send directly
NotificationBridge::send(AbstractPayload $payload, array $bridgeTokens);
```

###Queue push sending 
You can queue your push notification sending to improve your system performace

```
//Using Eloquent
$list = YourPushTokenTable::all();
 
//Create list for NotificationBridge
$bridgeTokens = [];
foreach ($list as $item){
    $bridgeTokens[] = new Token($item->platform, $item->device_token);
}

//push in queue
NotificationBridge::queue(AbstractPayload $payload, array $bridgeTokens, "queue-name");
```

With latest parameter you can shedule job in a specific queue. 

