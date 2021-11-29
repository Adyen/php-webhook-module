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

class CancelOrRefundProcessor extends Processor implements ProcessorInterface
{
    const MODIFICATION_ACTION = 'modification.action';
    const CANCEL = 'cancel';
    const REFUND = 'refund';

    public function process(): ?string
    {
        $state = $this->initialState;
        $logContext = [
            'eventCode' => EventCodes::CANCEL_OR_REFUND,
            'originalState' => $state
        ];

        if ($this->notification->isSuccess() && isset($this->notification->additionalData[self::MODIFICATION_ACTION])) {
            if ($this->notification->additionalData[self::MODIFICATION_ACTION] == self::CANCEL) {
                $state = PaymentStates::STATE_CANCELED;
            } elseif ($this->notification->additionalData[self::MODIFICATION_ACTION] == self::REFUND) {
                $state = PaymentStates::STATE_REFUNDED;
            }
        }

        $logContext['newState'] = $state;

        $this->log('info', 'Processed ' . EventCodes::CANCEL_OR_REFUND . ' notification.', $logContext);

        return $state;
    }
}
