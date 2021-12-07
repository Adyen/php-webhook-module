<?php

declare(strict_types=1);
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

namespace Adyen\Webhook\Test\Unit\Processor;

use Adyen\Webhook\EventCodes;
use Adyen\Webhook\PaymentStates;
use Adyen\Webhook\Processor\ProcessorFactory;

class RefundProcessorTest extends TestCase
{
    public function testRefundProcessorPaid()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::REFUND,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_PAID);
        $newState = $processor->process();

        $this->assertEquals(PaymentStates::STATE_REFUNDED, $newState);
    }

    public function testOrderClosedProcessorPartiallyRefunded()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::REFUND,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_PARTIALLY_REFUNDED);
        $newState = $processor->process();

        $this->assertEquals(PaymentStates::STATE_REFUNDED, $newState);
    }

    public function testOrderClosedProcessorRefundedFalse()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::REFUND,
                                                             'success' => 'false',
                                                         ]);
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_REFUNDED);
        $newState = $processor->process();

        $this->assertEquals(PaymentStates::STATE_PAID, $newState);
    }
}
