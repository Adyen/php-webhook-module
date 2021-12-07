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
            'success' => 'true',
        ]);
        $processor = ProcessorFactory::create($notification, 'refunded');

        $this->assertInstanceOf(RefundFailedProcessor::class, $processor);
    }

    public function testCreateAuthorisedProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'AUTHORISED',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'paid');

        $this->assertInstanceOf(AuthorisedProcessor::class, $processor);
    }

    public function testCreateCancelationProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'CANCELLATION',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(CancellationProcessor::class, $processor);
    }

    public function testCreateCanceledProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'CANCELLED',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(CancelledProcessor::class, $processor);
    }

    /**
     * @throws InvalidDataException
     */
    public function testCreateCancelOrRefundProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'CANCEL_OR_REFUND',
                                                             'success' => 'true',
                                                         ]);
        $notification->additionalData = array('modification.action'=>'cancel');
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(CancelOrRefundProcessor::class, $processor);
        $result = $processor->process($notification);
        $this->assertEquals(PaymentStates::STATE_CANCELLED, $result);
        $notification->additionalData = array('modification.action'=>'refund');
        $result = $processor->process($notification);
        $this->assertEquals(PaymentStates::STATE_REFUNDED, $result);
    }

    public function testCreateCaptureFailedProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'CAPTURE_FAILED',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(CapturedFailedProcessor::class, $processor);
    }

    public function testCreateCaptureProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'CAPTURE',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(CaptureProcessor::class, $processor);
    }

    public function testHandledExternallyProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'HANDLED_EXTERNALLY',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(HandledExternallyProcessor::class, $processor);
    }
    public function testManualReviewAcceptProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'MANUAL_REVIEW_ACCEPT',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(ManualReviewAcceptProcessor::class, $processor);
    }

    public function testManualReviewRejectProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'MANUAL_REVIEW_REJECT',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(ManualReviewRejectProcessor::class, $processor);
    }

    public function testOrderClosedProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'ORDER_CLOSED',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(OrderClosedProcessor::class, $processor);
    }

    public function testPendingProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'PENDING',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(PendingProcessor::class, $processor);
    }

    public function testRecurringContractProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'RECURRING_CONTRACT',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(RecurringContractProcessor::class, $processor);
    }

    public function testReportAvailableProcessor()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'REPORT_AVAILABLE',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');

        $this->assertInstanceOf(ReportAvailableProcessor::class, $processor);
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
