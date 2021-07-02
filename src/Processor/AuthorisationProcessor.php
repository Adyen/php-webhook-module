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

class AuthorisationProcessor extends Processor implements ProcessorInterface
{
    public function process(): void
    {
        $state = $this->paymentState;
        $logContext = [
            'eventCode' => EventCodes::AUTHORISATION,
            'originalState' => $state
        ];

        if ($this->notification->isSuccess()) {
            if ($state !== PaymentStates::STATE_PAID) {
                $this->setTransitionState(PaymentStates::STATE_PAID);
            }
        } else {
            if ($state === PaymentStates::STATE_IN_PROGRESS) {
                $this->setTransitionState(PaymentStates::STATE_FAILED);
            }
        }
        $logContext['newState'] = $this->transitionState;

        $this->log('info', 'Processed ' . EventCodes::AUTHORISATION . ' notification.', $logContext);
    }
}
