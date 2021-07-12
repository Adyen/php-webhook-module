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

namespace Adyen\Webhook;

use Adyen\Webhook\Exception\InvalidDataException;

class Notification
{
    public $eventCode;
    public $success;

    /**
     * @throws \Exception
     */
    public static function createItem(array $notificationData): Notification
    {
        self::validateNotificationData($notificationData);

        $notification = new self();
        $notification->eventCode = $notificationData['eventCode'];
        $notification->success = $notificationData['success'];

        return $notification;
    }

    public function getEventCode(): string
    {
        return $this->eventCode;
    }

    public function isSuccess(): bool
    {
        return in_array($this->success, [true, "true"]);
    }

    private static function validateNotificationData(array $data) {
        $class_vars = get_class_vars(self::class);

        $missing = [];
        $invalid = [];
        $eventCodes = new \ReflectionClass(EventCodes::class);

        foreach ($class_vars as $property => $value) {
            if (!isset($data[$property])) {
                $missing[] = $property;
            }
            if (isset($data['eventCode']) && !in_array($data['eventCode'], $eventCodes->getConstants())) {
                $invalid[] = 'eventCode';
            }
        }

        if (!empty($missing)) {
            throw new InvalidDataException('Field(s) missing from notification data: ' . join(', ', $missing));
        }

        if (!empty($invalid)) {
            throw new InvalidDataException('Invalid value for the field(s): ' . join($invalid));
        }
    }
}