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
    protected $paymentState;

    /**
     * @var null|string
     */
    protected $transitionState;

    abstract public function process(): void;

    public function __construct(Notification $notification, string $paymentState)
    {
        $this->notification = $notification;

        $paymentStatesClass = new \ReflectionClass(PaymentStates::class);
        if (!in_array($paymentState, $paymentStatesClass->getConstants())) {
            $this->log('error', 'Attempted to set an invalid state.', ['state' => $paymentState]);
            throw new \Exception('Invalid state.');
        }
        $this->paymentState = $paymentState;
    }

    public function getTransitionState(): string
    {
        return $this->transitionState;
    }

    protected function setTransitionState(string $state)
    {
        $this->transitionState = $state;
    }

    protected function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
