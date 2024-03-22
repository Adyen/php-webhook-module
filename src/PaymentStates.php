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
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_PENDING = 'pending';
    public const STATE_PAID = 'paid';
    public const STATE_AUTHORIZED = 'authorized';
    public const STATE_FAILED = 'failed';
    public const STATE_REFUNDED = 'refunded';
    public const STATE_PARTIALLY_REFUNDED = 'partially_refunded';
    public const STATE_CANCELLED = 'cancelled';
    public const STATE_NEW = 'new';
    public const CHARGE_BACK = "charge_back";
}
