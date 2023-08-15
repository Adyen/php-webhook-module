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
 * Copyright (c) 2022 Adyen N.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 */

namespace Adyen\Webhook\Processor;

use Adyen\Webhook\PaymentStates;

class RefundedWithDataProcessor extends Processor implements ProcessorInterface
{
    public function process(): ?string
    {
        $state = $this->initialState;

        if ($this->notification->isSuccess()) {
            if (PaymentStates::STATE_PAID === $state
                || PaymentStates::STATE_PARTIALLY_REFUNDED === $state) {
                $state = PaymentStates::STATE_REFUNDED;
            }
        } else {
            if (PaymentStates::STATE_REFUNDED === $state) {
                $state = PaymentStates::STATE_PAID;
            }
        }

        return $state;
    }
}
