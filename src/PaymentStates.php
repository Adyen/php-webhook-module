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

final class PaymentStates
{
    /**
     * Order states
     */
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_PAID = 'paid';
    public const STATE_FAILED = 'failed';
    public const STATE_REFUNDED = 'refunded';
    public const STATE_PARTIALLY_REFUNDED = 'partially_refunded';
    public const STATE_REFUND_FAILED = 'refund_failed';
    public const STATE_CANCELED = 'canceled';
    public const STATE_PAYMENT_REVIEW = 'payment_review';
    public const STATE_NEW = 'new';
    public const STATE_PROCESSING = 'progressing';
}
