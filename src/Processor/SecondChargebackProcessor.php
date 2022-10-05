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

use Adyen\Webhook\EventCodes;
use Adyen\Webhook\PaymentStates;

class SecondChargebackProcessor extends Processor implements ProcessorInterface
{
    public function process(): ?string
    {
        $state = $this->initialState;
        $logContext = [
            'eventCode' => EventCodes::SECOND_CHARGEBACK,
            'originalState' => $state
        ];

        if (in_array(
            $state,
            [PaymentStates::STATE_NEW, PaymentStates::STATE_IN_PROGRESS, PaymentStates::STATE_PENDING]
        )) {
            $state = $this->notification->isSuccess() ? PaymentStates::CHARGE_BACK : PaymentStates::STATE_FAILED;
        }
        $logContext['newState'] = $state;

        $this->log('info', 'Processed ' . EventCodes::SECOND_CHARGEBACK . ' notification.', $logContext);

        return $state;
    }
}
