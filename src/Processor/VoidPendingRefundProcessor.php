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

class VoidPendingRefundProcessor extends Processor implements ProcessorInterface
{
    public function process(): ?string
    {
        $state = $this->initialState;
        $logContext = [
            'eventCode' => EventCodes::VOID_PENDING_REFUND,
            'originalState' => $state
        ];

        if ($this->notification->isSuccess()
            && ($state === PaymentStates::STATE_NEW
                || $state === PaymentStates::STATE_PENDING
                || $state === PaymentStates::STATE_IN_PROGRESS)) {
            $state = PaymentStates::STATE_CANCELLED;
        }

        $logContext['newState'] = $state;

        $this->log('info', 'Processed ' . EventCodes::VOID_PENDING_REFUND . ' notification.', $logContext);

        return $state;
    }
}
