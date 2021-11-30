<?php


namespace Adyen\Webhook\Test\Unit\Processor;

use Adyen\Webhook\Notification;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createNotificationSuccess($notificationData): Notification
    {
        $notificationItem = Notification::createItem($notificationData);

        $this->assertInstanceOf(Notification::class, $notificationItem);

        return $notificationItem;
    }
}
