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

namespace Adyen\Webhook\Test\Unit\Processor;

use Adyen\Webhook\EventCodes;
use Adyen\Webhook\PaymentStates;
use Adyen\Webhook\Processor\ProcessorFactory;

class AuthorisationProcessorTest extends TestCase
{
    public function testAuthorisationProcessorNew()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::AUTHORISATION,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_NEW);
        $newState = $processor->process();

        $this->assertEquals(PaymentStates::STATE_PAID, $newState);
    }

    public function testAuthorisationProcessorProgress()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::AUTHORISATION,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_IN_PROGRESS);
        $newState = $processor->process();

        $this->assertEquals(PaymentStates::STATE_PAID, $newState);
    }

    public function testAuthorisationProcessorPending()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::AUTHORISATION,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, PaymentStates::STATE_PENDING);
        $newState = $processor->process();

        $this->assertEquals(PaymentStates::STATE_PAID, $newState);
    }
}
