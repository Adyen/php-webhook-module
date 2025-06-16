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
 * Copyright (c) 2025 Adyen N.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 */

namespace Adyen\Webhook\Processor;

use Adyen\Webhook\PaymentStates;

/**
 * When using this processor, set the initial state to:
 *   `partially_paid` if the payment is partially captured. (No order state change after processing.)
 *   `pending` if no payment has been captured, yet. (Order state changes to `cancelled` after processing.)
 *
 * This allows partial invoicing for the amount already captured and offline refunding for the expired
 * amount rather than cancelling the whole order.
 */
class ExpireProcessor extends Processor implements ProcessorInterface
{
    public function process(): ?string
    {
        $state = $this->initialState;
        $isAutoCapture = $this->isAutoCapture;

        if ($isAutoCapture === false && PaymentStates::STATE_PENDING === $state) {
            $state = PaymentStates::STATE_CANCELLED;
        } else {
            $state = $this->unchanged();
        }

        return $state;
    }
}
