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
use Adyen\Webhook\PaymentStates;

class AuthorisedProcessor extends Processor implements ProcessorInterface
{
    public function process(): ?string
    {
        $state = $this->initialState;
        $logContext = [
            'eventCode' => EventCodes::AUTHORISED,
            'originalState' => $state
        ];

        if ($this->notification->isSuccess()) {
            if (PaymentStates::STATE_NEW == $state
                || PaymentStates::STATE_IN_PROGRESS === $state
                || PaymentStates::STATE_PENDING === $state) {
                $state = PaymentStates::STATE_PAID;
            }
        }
        $logContext['newState'] = $state;

        $this->log('info', 'Processed ' . EventCodes::AUTHORISED . ' notification.', $logContext);

        return $state;
    }
}
