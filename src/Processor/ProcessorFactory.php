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

use Adyen\Service\ResourceModel\Modification\CancelOrRefund;
use Adyen\Service\ResourceModel\Modification\Capture;
use Adyen\Webhook\EventCodes;
use Adyen\Webhook\Exception\InvalidDataException;
use Adyen\Webhook\Notification;
use Psr\Log\LoggerInterface;

class ProcessorFactory
{
    private static $adyenEventCodeProcessors = [
        EventCodes::AUTHORISATION => AuthorisationProcessor::class,//ok
        EventCodes::OFFER_CLOSED => OfferClosedProcessor::class,
        EventCodes::REFUND => RefundProcessor::class,
        EventCodes::REFUND_FAILED => RefundFailedProcessor::class,
        EventCodes::PENDING => PendingProcessor::class,
        EventCodes::AUTHORISED => AuthorisedProcessor::class,
        EventCodes::RECEIVED => ReceivedProcessor::class,
        EventCodes::CANCELLED => CanceledProcessor::class,
        EventCodes::REFUSED => RefusedProcessor::class,
        EventCodes::ERROR => ErrorProcessor::class,
        EventCodes::CANCEL_OR_REFUND => CancelOrRefundProcessor::class,
        EventCodes::CAPTURE => CaptureProcessor::class,
        EventCodes::CAPTURE_FAILED => CapturedFailedProcessor::class,
        EventCodes::CANCELLATION => CancelationProcessor::class,
        EventCodes::HANDLED_EXTERNALLY => HandledExternallyProcessor::class,
        EventCodes::MANUAL_REVIEW_ACCEPT => ManualReviewAcceptProcessor::class,
        EventCodes::MANUAL_REVIEW_REJECT => ManualReviewRejectProcessor::class,
        EventCodes::RECURRING_CONTRACT => RecurringContractProcessor::class,
        EventCodes::REPORT_AVAILABLE => ReportAvailableProcessor::class,
        EventCodes::ORDER_CLOSED => OrderClosedProcessor::class
    ];


    /**
     * @throws InvalidDataException
     */
    public static function create(
        Notification $notification,
        string $paymentState,
        LoggerInterface $logger = null
    ): ProcessorInterface {
        /** @var Processor $processor */
        $processor = array_key_exists($notification->getEventCode(), self::$adyenEventCodeProcessors)
            ? new self::$adyenEventCodeProcessors[$notification->getEventCode()]($notification, $paymentState)
            : new DefaultProcessor($notification, $paymentState);

        if ($logger) {
            $processor->setLogger($logger);
        }

        return $processor;
    }
}
