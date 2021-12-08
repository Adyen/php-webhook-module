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

use Adyen\Webhook\EventCodes;
use Adyen\Webhook\Exception\InvalidDataException;
use Adyen\Webhook\Notification;
use Adyen\Webhook\PaymentStates;
use Adyen\Webhook\Processor\AuthorisationProcessor;
use Adyen\Webhook\Processor\AuthorisedProcessor;
use Adyen\Webhook\Processor\CancellationProcessor;
use Adyen\Webhook\Processor\CancelledProcessor;
use Adyen\Webhook\Processor\CancelOrRefundProcessor;
use Adyen\Webhook\Processor\CapturedFailedProcessor;
use Adyen\Webhook\Processor\CaptureProcessor;
use Adyen\Webhook\Processor\HandledExternallyProcessor;
use Adyen\Webhook\Processor\ManualReviewAcceptProcessor;
use Adyen\Webhook\Processor\ManualReviewRejectProcessor;
use Adyen\Webhook\Processor\OfferClosedProcessor;
use Adyen\Webhook\Processor\OrderClosedProcessor;
use Adyen\Webhook\Processor\PendingProcessor;
use Adyen\Webhook\Processor\ProcessorFactory;
use Adyen\Webhook\Processor\RecurringContractProcessor;
use Adyen\Webhook\Processor\RefundFailedProcessor;
use Adyen\Webhook\Processor\RefundProcessor;


use Adyen\Webhook\Processor\ReportAvailableProcessor;

class ProcessorFactoryTest extends TestCase
{
    /**
     * Data provider to test the ProcessorFactory. The Payment State is tested here
     * @return array[]
     */
    public function eventCodesProvider()
    {
        return [
            [EventCodes::AUTHORISED, AuthorisedProcessor::class, 'in_progress'],
            [EventCodes::AUTHORISATION, AuthorisationProcessor::class, 'in_progress'],
            [EventCodes::CANCELLATION, CancellationProcessor::class, 'in_progress'],
            [EventCodes::CANCELLED, CancelledProcessor::class, 'in_progress'],
            [EventCodes::CANCEL_OR_REFUND, CancelOrRefundProcessor::class, 'in_progress'],
            [EventCodes::CAPTURE_FAILED, CapturedFailedProcessor::class, 'in_progress'],
            [EventCodes::CAPTURE, CaptureProcessor::class, 'in_progress'],
            [EventCodes::HANDLED_EXTERNALLY, HandledExternallyProcessor::class, 'in_progress'],
            [EventCodes::MANUAL_REVIEW_ACCEPT, ManualReviewAcceptProcessor::class, 'in_progress'],
            [EventCodes::MANUAL_REVIEW_REJECT, ManualReviewRejectProcessor::class, 'in_progress'],
            [EventCodes::OFFER_CLOSED, OfferClosedProcessor::class, 'in_progress'],
            [EventCodes::ORDER_CLOSED, OrderClosedProcessor::class, 'in_progress'],
            [EventCodes::PENDING, PendingProcessor::class, 'in_progress'],
            [EventCodes::RECURRING_CONTRACT, RecurringContractProcessor::class, 'in_progress'],
            [EventCodes::REFUND, RefundProcessor::class, 'in_progress'],
            [EventCodes::REFUND_FAILED, RefundFailedProcessor::class, 'in_progress'],
            [EventCodes::REPORT_AVAILABLE, ReportAvailableProcessor::class, 'in_progress']
        ];
    }

    /**
     * @dataProvider eventCodesProvider
     */
    public function testCreate($event, $expectedProcessor, $currentState)
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => $event,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, $currentState);

        $this->assertInstanceOf($expectedProcessor, $processor);
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

    public static function invalidNotificationData(): array
    {
        return [
            [
                [],
                ['error' => true, 'errorMessage' => 'Field(s) missing from notification data: eventCode, success'],
            ],
            [
                ['eventCode' => 'foobar', 'success' => true],
                ['error' => true, 'errorMessage' => 'Invalid value for the field(s) with key(s): eventCode']
            ]
        ];
    }
}
