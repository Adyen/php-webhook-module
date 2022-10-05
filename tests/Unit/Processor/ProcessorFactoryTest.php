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
use Adyen\Webhook\Processor\AuthorisationAdjustmentProcessor;
use Adyen\Webhook\Processor\AuthorisationProcessor;
use Adyen\Webhook\Processor\AuthorisedProcessor;
use Adyen\Webhook\Processor\CancellationProcessor;
use Adyen\Webhook\Processor\CancelledProcessor;
use Adyen\Webhook\Processor\CancelOrRefundProcessor;
use Adyen\Webhook\Processor\CapturedFailedProcessor;
use Adyen\Webhook\Processor\CaptureProcessor;
use Adyen\Webhook\Processor\ChargebackProcessor;
use Adyen\Webhook\Processor\ChargebackReversedProcessor;
use Adyen\Webhook\Processor\HandledExternallyProcessor;
use Adyen\Webhook\Processor\ManualReviewAcceptProcessor;
use Adyen\Webhook\Processor\ManualReviewRejectProcessor;
use Adyen\Webhook\Processor\OfferClosedProcessor;
use Adyen\Webhook\Processor\OrderClosedProcessor;
use Adyen\Webhook\Processor\OrderOpenedProcessor;
use Adyen\Webhook\Processor\PendingProcessor;
use Adyen\Webhook\Processor\ProcessorFactory;
use Adyen\Webhook\Processor\RecurringContractProcessor;
use Adyen\Webhook\Processor\RefundedReversedProcessor;
use Adyen\Webhook\Processor\RefundedWithDataProcessor;
use Adyen\Webhook\Processor\RefundFailedProcessor;
use Adyen\Webhook\Processor\RefundProcessor;


use Adyen\Webhook\Processor\ReportAvailableProcessor;
use Adyen\Webhook\Processor\SecondChargebackProcessor;
use Adyen\Webhook\Processor\VoidPendingRefundProcessor;

class ProcessorFactoryTest extends TestCase
{
    /**
     * Data provider to test the ProcessorFactory. The Payment State is not tested here
     * @return array[]
     */
    public function eventCodesProvider(): array
    {
        return [
            [EventCodes::AUTHORISATION, AuthorisationProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CANCELLATION, CancellationProcessor::class, PaymentStates::STATE_IN_PROGRESS],
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
            [EventCodes::REPORT_AVAILABLE, ReportAvailableProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::AUTHORISATION_ADJUSTMENT, AuthorisationAdjustmentProcessor::class,
                PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::ORDER_OPENED, OrderOpenedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::REFUNDED_REVERSED, RefundedReversedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::REFUND_WITH_DATA, RefundedWithDataProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::VOID_PENDING_REFUND, VoidPendingRefundProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CHARGEBACK, ChargebackProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::CHARGEBACK_REVERSED, ChargebackReversedProcessor::class, PaymentStates::STATE_IN_PROGRESS],
            [EventCodes::SECOND_CHARGEBACK, SecondChargebackProcessor::class, PaymentStates::STATE_IN_PROGRESS]
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

    public static function invalidNotificationData(): array
    {
        return [
            [
                [],
                ['error' => true, 'errorMessage' => 'Field(s) missing from notification data: eventCode, success'],
            ],
            [
                ['eventCode' => 'foobar', 'success' => true],
                ['error' => true, 'errorMessage' => 'Invalid value for the field(s) with key(s): foobar']
            ]
        ];
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

    /*
     * Data provider with arguments: event code, original payment state, expected payment state and success
     */
    public function processorPaymentStatesProvider(): array
    {
        return [
            [EventCodes::AUTHORISATION, PaymentStates::STATE_NEW, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::AUTHORISATION, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::AUTHORISATION, PaymentStates::STATE_PENDING, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::CANCELLATION, PaymentStates::STATE_NEW, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::CANCELLATION, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::CANCELLATION, PaymentStates::STATE_PENDING, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::CAPTURE_FAILED, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::CAPTURE, PaymentStates::STATE_NEW, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::CAPTURE, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::CAPTURE, PaymentStates::STATE_PENDING, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::HANDLED_EXTERNALLY, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::MANUAL_REVIEW_REJECT, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::MANUAL_REVIEW_ACCEPT, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::OFFER_CLOSED, PaymentStates::STATE_NEW, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::OFFER_CLOSED, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::OFFER_CLOSED, PaymentStates::STATE_PENDING, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_NEW, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_PENDING, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_NEW, PaymentStates::STATE_CANCELLED, 'false'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_CANCELLED, 'false'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_PENDING, PaymentStates::STATE_CANCELLED, 'false'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_PAID, PaymentStates::STATE_REFUNDED, 'false'],
            [EventCodes::ORDER_CLOSED, PaymentStates::STATE_PARTIALLY_REFUNDED, PaymentStates::STATE_REFUNDED, 'false'],
            [EventCodes::PENDING, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::RECURRING_CONTRACT, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::REPORT_AVAILABLE, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::REFUND_FAILED, PaymentStates::STATE_REFUNDED, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::REFUND, PaymentStates::STATE_PAID, PaymentStates::STATE_REFUNDED, 'true'],
            [EventCodes::REFUND, PaymentStates::STATE_PARTIALLY_REFUNDED, PaymentStates::STATE_REFUNDED, 'true'],
            [EventCodes::REFUND, PaymentStates::STATE_REFUNDED, PaymentStates::STATE_PAID, 'false'],
            [EventCodes::AUTHORISATION_ADJUSTMENT, PaymentStates::STATE_NEW, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::AUTHORISATION_ADJUSTMENT, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::AUTHORISATION_ADJUSTMENT, PaymentStates::STATE_PENDING, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::ORDER_OPENED, PaymentStates::STATE_PENDING, PaymentStates::STATE_PENDING, 'true'],
            [EventCodes::ORDER_OPENED, PaymentStates::STATE_NEW, PaymentStates::STATE_NEW, 'true'],
            [EventCodes::REFUNDED_REVERSED, PaymentStates::STATE_REFUNDED, PaymentStates::STATE_PAID, 'true'],
            [EventCodes::REFUND_WITH_DATA, PaymentStates::STATE_PAID, PaymentStates::STATE_REFUNDED, 'true'],
            [EventCodes::REFUND_WITH_DATA, PaymentStates::STATE_PARTIALLY_REFUNDED, PaymentStates::STATE_REFUNDED,
                'true'],
            [EventCodes::REFUND_WITH_DATA, PaymentStates::STATE_REFUNDED, PaymentStates::STATE_PAID, 'false'],
            [EventCodes::VOID_PENDING_REFUND, PaymentStates::STATE_NEW, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::VOID_PENDING_REFUND, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::VOID_PENDING_REFUND, PaymentStates::STATE_PENDING, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::CHARGEBACK, PaymentStates::STATE_NEW, PaymentStates::CHARGE_BACK, 'true'],
            [EventCodes::CHARGEBACK, PaymentStates::STATE_IN_PROGRESS, PaymentStates::CHARGE_BACK, 'true'],
            [EventCodes::CHARGEBACK, PaymentStates::STATE_PENDING, PaymentStates::CHARGE_BACK, 'true'],
            [EventCodes::CHARGEBACK_REVERSED, PaymentStates::STATE_NEW, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::CHARGEBACK_REVERSED, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::CHARGEBACK_REVERSED, PaymentStates::STATE_PENDING, PaymentStates::STATE_CANCELLED, 'true'],
            [EventCodes::SECOND_CHARGEBACK, PaymentStates::STATE_NEW, PaymentStates::CHARGE_BACK, 'true'],
            [EventCodes::SECOND_CHARGEBACK, PaymentStates::STATE_IN_PROGRESS, PaymentStates::CHARGE_BACK, 'true'],
            [EventCodes::SECOND_CHARGEBACK, PaymentStates::STATE_PENDING, PaymentStates::CHARGE_BACK, 'true'],
            [EventCodes::SECOND_CHARGEBACK, PaymentStates::STATE_NEW, PaymentStates::STATE_FAILED, 'false'],
            [EventCodes::SECOND_CHARGEBACK, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_FAILED, 'false'],
            [EventCodes::SECOND_CHARGEBACK, PaymentStates::STATE_PENDING, PaymentStates::STATE_FAILED, 'false']
        ];
    }

    /**
     * @dataProvider processorPaymentStatesProvider
     */
    public function testProcessorPaymentStates($eventCode, $originalState, $expectedState, $success)
    {
        $notification = $this->createNotificationSuccess([
            'eventCode' => $eventCode,
            'success' => $success,
        ]);
        $processor = ProcessorFactory::create($notification, $originalState);
        $newState = $processor->process();
        $this->assertEquals($expectedState, $newState);
    }

    public function testCreateCancelOrRefundProcessorRefund()
    {
        $notification = $this->createNotificationSuccess(
            [
                'eventCode' => EventCodes::CANCEL_OR_REFUND,
                'success' => 'true',
            ]
        );
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_PAID);
        $notification->additionalData = array('modification.action' => 'refund');
        $result = $processor->process($notification);
        $this->assertEquals(PaymentStates::STATE_REFUNDED, $result);
    }

    public function testCreateCancelOrRefundProcessorCancel()
    {
        $notification = $this->createNotificationSuccess(
            [
                'eventCode' => 'CANCEL_OR_REFUND',
                'success' => 'true',
            ]
        );
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_IN_PROGRESS);
        $notification->additionalData = array('modification.action' => 'cancel');
        $result = $processor->process($notification);
        $this->assertEquals(PaymentStates::STATE_CANCELLED, $result);
    }
}
