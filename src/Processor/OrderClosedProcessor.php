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

use Adyen\Webhook\PaymentStates;

class OrderClosedProcessor extends Processor implements ProcessorInterface
{
    public function process(): ?string
    {
        $state = $this->initialState;

        if ($this->notification->isSuccess()) {
            if (PaymentStates::STATE_NEW === $state
                || PaymentStates::STATE_IN_PROGRESS === $state
                || PaymentStates::STATE_PENDING === $state) {
                $state = PaymentStates::STATE_PAID;
            }
        } else {
            if (PaymentStates::STATE_NEW === $state
                || PaymentStates::STATE_IN_PROGRESS === $state
                || PaymentStates::STATE_PENDING === $state) {
                $state = PaymentStates::STATE_CANCELLED;
            } elseif (PaymentStates::STATE_PAID === $state
                || PaymentStates::STATE_PARTIALLY_REFUNDED === $state) {
                $state = PaymentStates::STATE_REFUNDED;
            }
        }

        return $state;
    }
}
