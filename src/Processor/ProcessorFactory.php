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

namespace Adyen\Webhook\Processor;

use Adyen\Webhook\EventCodes;
use Adyen\Webhook\Exception\InvalidDataException;
use Adyen\Webhook\Notification;

class ProcessorFactory
{
    private static array $adyenEventCodeProcessors = [
        EventCodes::AUTHORISATION => AuthorisationProcessor::class,
        EventCodes::OFFER_CLOSED => OfferClosedProcessor::class,
        EventCodes::REFUND => RefundProcessor::class,
        EventCodes::REFUND_FAILED => RefundFailedProcessor::class,
        EventCodes::PENDING => PendingProcessor::class,
        EventCodes::CANCEL_OR_REFUND => CancelOrRefundProcessor::class,
        EventCodes::CAPTURE => CaptureProcessor::class,
        EventCodes::CAPTURE_FAILED => CapturedFailedProcessor::class,
        EventCodes::CANCELLATION => CancellationProcessor::class,
        EventCodes::HANDLED_EXTERNALLY => HandledExternallyProcessor::class,
        EventCodes::MANUAL_REVIEW_ACCEPT => ManualReviewAcceptProcessor::class,
        EventCodes::MANUAL_REVIEW_REJECT => ManualReviewRejectProcessor::class,
        EventCodes::RECURRING_CONTRACT => RecurringContractProcessor::class,
        EventCodes::REPORT_AVAILABLE => ReportAvailableProcessor::class,
        EventCodes::ORDER_CLOSED => OrderClosedProcessor::class,
        EventCodes::AUTHORISATION_ADJUSTMENT => AuthorisationAdjustmentProcessor::class,
        EventCodes::ORDER_OPENED => OrderOpenedProcessor::class,
        EventCodes::REFUNDED_REVERSED => RefundedReversedProcessor::class,
        EventCodes::REFUND_WITH_DATA => RefundedWithDataProcessor::class,
        EventCodes::VOID_PENDING_REFUND => VoidPendingRefundProcessor::class,
        EventCodes::CHARGEBACK => ChargebackProcessor::class,
        EventCodes::CHARGEBACK_REVERSED => ChargebackReversedProcessor::class,
        EventCodes::SECOND_CHARGEBACK => SecondChargebackProcessor::class,
        EventCodes::NOTIFICATION_OF_CHARGEBACK => NotificationOfChargebackProcessor::class,
        EventCodes::REQUEST_FOR_INFORMATION => RequestForInformationProcessor::class,
        EventCodes::EXPIRE => ExpireProcessor::class,
        EventCodes::RECURRING_TOKEN_CREATED => TokenLifecycleEventProcessor::class,
        EventCodes::RECURRING_TOKEN_UPDATED => TokenLifecycleEventProcessor::class,
        EventCodes::RECURRING_TOKEN_DISABLED => TokenLifecycleEventProcessor::class,
        EventCodes::RECURRING_TOKEN_ALREADY_EXISTING => TokenLifecycleEventProcessor::class
    ];

    /**
     * @param Notification $notification
     * @param string $paymentState
     * @param bool $isAutoCapture
     * @return ProcessorInterface
     * @throws InvalidDataException
     */
    public static function create(
        Notification    $notification,
        string          $paymentState,
        bool            $isAutoCapture = true
    ): ProcessorInterface {
        return array_key_exists($notification->getEventCode(), self::$adyenEventCodeProcessors)
            ? new self::$adyenEventCodeProcessors[$notification->getEventCode()](
                $notification,
                $paymentState,
                $isAutoCapture
            )
            : new DefaultProcessor($notification, $paymentState, $isAutoCapture);
    }
}
