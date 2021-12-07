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

class CancelOrRefundProcessorTest extends TestCase
{
    public function testCreateCancelOrRefundProcessorRefund()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => EventCodes::CANCEL_OR_REFUND,
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');
        $notification->additionalData = array('modification.action'=>'refund');
        $result = $processor->process($notification);
        $this->assertEquals(PaymentStates::STATE_REFUNDED, $result);
    }

    public function testCreateCancelOrRefundProcessorCancel()
    {
        $notification = $this->createNotificationSuccess([
                                                             'eventCode' => 'CANCEL_OR_REFUND',
                                                             'success' => 'true',
                                                         ]);
        $processor = ProcessorFactory::create($notification, 'in_progress');
        $notification->additionalData = array('modification.action'=>'cancel');
        $result = $processor->process($notification);
        $this->assertEquals(PaymentStates::STATE_CANCELLED, $result);
    }
}
