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
    public function process(): ?string
    {
        $state = $this->initialState;
        $logContext = [
            'eventCode' => EventCodes::OFFER_CLOSED,
            'originalState' => $state
        ];
        if ($this->notification->isSuccess() && PaymentStates::STATE_CANCELED === $state) {
            $this->_adyenLogger->addAdyenNotificationCronjob(
                "Order is already cancelled, skipping OFFER_CLOSED"
            );
        }
        if ($this->notification->isSuccess() && PaymentStates::AUTHORISED === $state) {
            $this->_adyenLogger->addAdyenNotificationCronjob(
                "Order is already cancelled, skipping OFFER_CLOSED"
            );
        }

        if ($this->notification->isSuccess() && PaymentStates::STATE_IN_PROGRESS === $state) {
            $state = PaymentStates::STATE_FAILED;
        }
        $logContext['newState'] = $state;

        $this->log('info', 'Processed ' . EventCodes::OFFER_CLOSED . ' notification.', $logContext);

        return $state;
    }
}
