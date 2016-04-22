# push-notification
Extensible Laravel package to send push notification across all platform

#Platform actually supported
- iOS - apns (Apple Push Notificatio Service)
- android - GCM (Google Cloud Messaging)

#Install
Add follow line into "require" section in your composer.json:

"developerdynamo/push-notification": "1.*"

Update composer with command:

"composer update"

#Configure Laravel 5
Like all providers, put this follow lines in your config/app.php

'providers' => [
	...
	DeveloperDynamo\PushNotification\PushNotificationProvider::class,
],
 
'aliases' => [
	...
	'NotificationBridge' => DeveloperDynamo\PushNotification\Facades\PushNotificationBridge::class,
],

#Publish configuration
Finally you need to generate a configuration file for this package.
Run follow composer command:

php artisan vendor:publish --provider="DeveloperDynamo\PushNotification\PushNotificationProvider"

