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

class OfferClosedProcessor extends Processor implements ProcessorInterface
{
    public function process(): void
    {
        $state = $this->paymentState;
        $logContext = [
            'eventCode' => EventCodes::OFFER_CLOSED,
            'originalState' => $state
        ];

        if ($this->notification->isSuccess() && $state === PaymentStates::STATE_IN_PROGRESS) {
            $this->setTransitionState(PaymentStates::STATE_FAILED);
        }
        $logContext['newState'] = $this->transitionState;

        $this->log('info', 'Processed ' . EventCodes::OFFER_CLOSED . ' notification.', $logContext);
    }
}
