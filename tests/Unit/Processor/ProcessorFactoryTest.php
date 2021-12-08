<?php
declare(strict_types=1);
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

    /**
     * Data provider to test the ProcessorFactory. The Payment State is not tested here
     * @return array[]
     */
    public function eventCodesProvider()
    {
        return [
            [EventCodes::AUTHORISED, AuthorisedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::AUTHORISATION, AuthorisationProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CANCELLATION, CancellationProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CANCELLED, CancelledProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CANCEL_OR_REFUND, CancelOrRefundProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CAPTURE_FAILED, CapturedFailedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CAPTURE, CaptureProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::HANDLED_EXTERNALLY, HandledExternallyProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::MANUAL_REVIEW_ACCEPT, ManualReviewAcceptProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::MANUAL_REVIEW_REJECT, ManualReviewRejectProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::OFFER_CLOSED, OfferClosedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::ORDER_CLOSED, OrderClosedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::PENDING, PendingProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::RECURRING_CONTRACT, RecurringContractProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::REFUND, RefundProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::REFUND_FAILED, RefundFailedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::REPORT_AVAILABLE, ReportAvailableProcessor::class, PaymentStates::STATE_IN_PROGRESS]
        ];
    }

    /**
     * @dataProvider eventCodesProvider
     */
    public function testCreate($event, $expectedProcessor, $currentState)
    {
        $notification = $this->createNotificationSuccess(
            [
                'eventCode' => $event,
                'success' => 'true',
            ]
        );
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
}
