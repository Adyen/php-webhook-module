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

abstract class AbstractDisputeNotificationProcessor extends Processor implements ProcessorInterface
{
    const DISPUTE_STATUS_UNDEFENDED = "Undefended";
    const DISPUTE_STATUS_LOST = "Lost";
    const DISPUTE_STATUS_ACCEPTED = "Accepted";
    const FINAL_DISPUTE_STATUSES = [
        self::DISPUTE_STATUS_LOST,
        self::DISPUTE_STATUS_ACCEPTED,
        self::DISPUTE_STATUS_UNDEFENDED
    ];
    const DISPUTE_STATUS = "disputeStatus";


    const ORDER_ARRAY = [
        PaymentStates::STATE_PAID,
        PaymentStates::STATE_IN_PROGRESS,
    ];

    public function process(): ?string
    {
        $state = $this->initialState;
        $additionalData = $this->notification->getAdditionalData();
        $disputeStatus = $additionalData[self::DISPUTE_STATUS] ?? null;

        if ($this->notification->isSuccess() &&
            isset($disputeStatus) &&
            in_array($disputeStatus, self::FINAL_DISPUTE_STATUSES)) {
            //State should be In Progress or paid in cases where we want to change it to refunded.
            if (in_array($state, self::ORDER_ARRAY)) {
                $state = PaymentStates::STATE_REFUNDED;
            }
        }

        return $state;
    }
}