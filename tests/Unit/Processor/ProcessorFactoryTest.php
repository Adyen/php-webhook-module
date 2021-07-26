<?php declare(strict_types=1);
/**
 *                       ######
 *                       ######
 * ############    ####( ######  #####. ######  ############   ############
 * #############  #####( ######  #####. ######  #############  #############
 *        ######  #####( ######  #####. ######  #####  ######  #####  ######
 * ###### ######  #####( ######  #####. ######  #####  #####   #####  ######
 * ###### ######  #####( ######  #####. ######  #####          #####  ######
 * #############  #############  #############  #############  #####  ######
 *  ############   ############  #############   ############  #####  ######
 *                                      ######
 *                               #############
 *                               ############
 *
 * Adyen Webhook Module for PHP
 *
 * Copyright (c) 2021 Adyen N.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 */

namespace Adyen\Webhook\Test\Unit\Processor;

use Adyen\Webhook\Exception\InvalidDataException;
use Adyen\Webhook\Notification;
use Adyen\Webhook\Processor\AuthorisationProcessor;
use Adyen\Webhook\Processor\OfferClosedProcessor;
use Adyen\Webhook\Processor\ProcessorFactory;
use Adyen\Webhook\Processor\RefundFailedProcessor;
use Adyen\Webhook\Processor\RefundProcessor;
use PHPUnit\Framework\TestCase;

class ProcessorFactoryTest extends TestCase
{
    private function createNotificationSuccess($notificationData): Notification
    {
        $notificationItem = Notification::createItem($notificationData);

        $this->assertInstanceOf(Notification::class, $notificationItem);

        return $notificationItem;
    }

    /**
     * @dataProvider invalidNotificationData
     */
    public function testCreateNotificationFail($notificationData, $result)
    {
        if ($result['error']) {
            $this->expectException(InvalidDataException::class);
            $this->expectErrorMessage($result['errorMessage']);
        }
        Notification::createItem($notificationData);
    }

    public function testCreateAuthorisationProcessor()
    {
        $notification = $this->createNotificationSuccess([
            'eventCode' => 'AUTHORISATION',
            'success' => 'true',
        ]);
        $processor = ProcessorFactory::create($notification, 'paid');

        $this->assertInstanceOf(AuthorisationProcessor::class, $processor);
    }

    public function testCreateOfferClosedProcessor()
    {
        $notification = $this->createNotificationSuccess([
            'eventCode' => 'OFFER_CLOSED',
            'success' => 'true',
        ]);
        $processor = ProcessorFactory::create($notification, 'paid');

        $this->assertInstanceOf(OfferClosedProcessor::class, $processor);
    }

    public function testCreateRefundProcessor()
    {
        $notification = $this->createNotificationSuccess([
            'eventCode' => 'REFUND',
            'success' => 'true',
        ]);
        $processor = ProcessorFactory::create($notification, 'paid');

        $this->assertInstanceOf(RefundProcessor::class, $processor);
    }

    public function testCreateRefundFailedProcessor()
    {
        $notification = $this->createNotificationSuccess([
            'eventCode' => 'REFUND_FAILED',
        ]);
        $processor = ProcessorFactory::create($notification, 'refunded');

        $this->assertInstanceOf(RefundFailedProcessor::class, $processor);
    }

    public static function invalidNotificationData(): array
    {
        return [
            [
                [],
                ['error' => true, 'errorMessage' => 'Field(s) missing from notification data: eventCode'],
            ],
            [
                ['eventCode' => 'foobar', 'success' => true],
                ['error' => true, 'errorMessage' => 'Invalid value for the field(s): eventCode']
            ]
        ];
    }
}
