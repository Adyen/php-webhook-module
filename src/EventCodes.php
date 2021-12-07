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

namespace Adyen\Webhook;

final class EventCodes
{
    const AUTHORISATION = 'AUTHORISATION';
    const PENDING = 'PENDING';
    const AUTHORISED = 'AUTHORISED';
    const CANCELLED = 'CANCELLED';
    const REFUND = 'REFUND';
    const REFUND_FAILED = 'REFUND_FAILED';
    const CANCEL_OR_REFUND = 'CANCEL_OR_REFUND';
    const CAPTURE = 'CAPTURE';
    const CAPTURE_FAILED = 'CAPTURE_FAILED';
    const CANCELLATION = 'CANCELLATION';
    const HANDLED_EXTERNALLY = 'HANDLED_EXTERNALLY';
    const MANUAL_REVIEW_ACCEPT = 'MANUAL_REVIEW_ACCEPT';
    const MANUAL_REVIEW_REJECT = 'MANUAL_REVIEW_REJECT';
    const RECURRING_CONTRACT = "RECURRING_CONTRACT";
    const REPORT_AVAILABLE = "REPORT_AVAILABLE";
    const ORDER_CLOSED = "ORDER_CLOSED";
    const OFFER_CLOSED = "OFFER_CLOSED";
}
