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

use Adyen\Webhook\Exception\InvalidDataException;
use Adyen\Webhook\Notification;
use Adyen\Webhook\PaymentStates;
use Psr\Log\LoggerAwareTrait;

abstract class Processor implements ProcessorInterface
{
    use LoggerAwareTrait;

    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @var string
     */
    protected $initialState;

    abstract public function process(): ?string;

    public function __construct(Notification $notification, string $state)
    {
        $this->notification = $notification;
        $this->validateState($state);

        $this->initialState = $state;
    }

    protected function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    protected function validateState($state)
    {
        $paymentStatesClass = new \ReflectionClass(PaymentStates::class);
        if (!in_array($state, $paymentStatesClass->getConstants())) {
            $this->log('error', 'Attempted to set an invalid state.', ['state' => $state]);
            throw new InvalidDataException('Invalid state.');
        }
    }

    /**
     * In case of unchanged payment state based on notification log
     * the eventCode, originalState and newState
     * @param string $eventCode
     * @return string
     */
    protected function unchanged(string $eventCode): string
    {
        $state = $this->initialState;
        $this->log(
            'info',
            'Processed ' . $eventCode . ' notification.',
            [
                'eventCode' => $eventCode,
                'originalState' => $state,
                'newState' => $state
            ]
        );
        return $state;
    }
}
