# Webhook Module for PHP (Beta)

Adyen library for handling notification webhooks.

## Installation
You can use [Composer](https://getcomposer.org/). Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have Composer installed.

~~~~ bash
composer require adyen/php-webhook-module
~~~~

In your PHP script, make sure to include the autoloader:

~~~~ php
require __DIR__ . '/vendor/autoload.php';
~~~~

Alternatively, you can download the [release on GitHub](https://github.com/Adyen/php-webhook-module/releases).

## Usage

#### Authenticate and validate incoming webhook request:
~~~~ php 
// Setup NotificationReceiver with dependency injection or create an instance as follows
$notificationReceiver = new \Adyen\Webhook\Receiver\NotificationReceiver(new \Adyen\Webhook\Receiver\HmacSignature);

// Authorize notification
if (!$notificationReceiver->isAuthenticated(
    $request['notificationItems'][0]['NotificationRequestItem'],
    YOUR_MERCHANT_ACCOUNT,
    YOUR_NOTIFICATION_USERNAME,
    YOUR_NOTIFICATION_PASSWORD
)) {
    throw new AuthenticationException();
}

// Process each notification item
foreach ($request['notificationItems'] as $notificationItem) {
    // validate the notification
    if ($notificationReceiver->validateHmac($notificationItem, YOUR_HMAC_KEY)) {
       // save notification to your database
       $this->databaseService->saveNotification($notificationItem); 
    }
}

return new JsonResponse('[accepted]');
~~~~

#### Process notification to get new payment state:
~~~~ php 
$notificationItem = \Adyen\Webhook\Notification::createItem([
    'eventCode' => $notification['eventCode'],
    'success' => $notification['success']
]);

$processor = \Adyen\Webhook\Processor\ProcessorFactory::create(
    $notificationItem,
    $currentPaymentState,
    $this->logger
);

$newState = $processor->process();
~~~~
NB: set `$currentPaymentState` to one of the values in `\Adyen\Webhook\PaymentStates`

## Documentation
Visit our [documentation page](https://docs.adyen.com/development-resources/webhooks/understand-notifications) to learn more about handling notifications.

## Contributing
We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements.
Have a look at our [contributing guidelines](https://github.com/Adyen/.github/blob/develop/CONTRIBUTING.md) to find out how to raise a pull request.

## Support
If you have a feature request, or spotted a bug or a technical problem, [create an issue here](https://github.com/Adyen/php-webhook-module/issues/new/choose).
For other questions, [contact our Support Team](https://www.adyen.help/hc/en-us/requests/new?ticket_form_id=360000705420).

## Licence
This repository is available under the [MIT license](https://github.com/Adyen/php-webhook-module/blob/master/LICENSE).
