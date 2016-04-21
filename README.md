# push-notification
Extensible Laravel package to send push notification across all platform

#Platform actually suported
- iOS - apns (Apple Push Notificatio Service)
- android - GCM (Google Cloud Messaging)

#Install
Add follow line into "require" section in your composer.json:

"developerdynamo/push-notification": "1.*"

Update composer with command:

"composer update"

#Add Notification Provider in Laravel 5
Like all providers, add them to your config/app.php.

/*
 * Push Notification Service Providers
 */
'DeveloperDynamo\PushNotification\PushNotificationProvider',
 