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
    const PROPERTY_EVENT_CODE = 'eventCode';
    const PROPERTY_SUCCESS = 'success';
    const ADDITIONAL_DATA ='additionalData';

    const REQUIRED_PROPERTIES = [
        self::PROPERTY_EVENT_CODE,
        self::PROPERTY_SUCCESS
    ];

    public $eventCode;
    public $success;
    public $additionalData;

    /**
     * @throws InvalidDataException
     */
    public static function createItem(array $notificationData): Notification
    {
        self::validateNotificationData($notificationData);

        $notification = new self();
        $notification->eventCode = $notificationData[self::PROPERTY_EVENT_CODE];
        $notification->success = $notificationData[self::PROPERTY_SUCCESS];

        if (isset($notificationData[self::ADDITIONAL_DATA]) && is_array($notificationData[self::ADDITIONAL_DATA])) {
            $notification->additionalData = $notificationData[self::ADDITIONAL_DATA];
        }

        return $notification;
    }

    public function getEventCode(): string
    {
        return $this->eventCode;
    }

    public function isSuccess(): bool
    {
        return in_array($this->success, [true, "true"], true);
    }

    private static function validateNotificationData(array $data)
    {
        $missing = [];
        $invalid = [];
        $eventCodes = new \ReflectionClass(EventCodes::class);

        foreach (self::REQUIRED_PROPERTIES as $property) {
            // If required data is missing
            if (!isset($data[$property])) {
                $missing[] = $property;
            }

            // If an invalid event code is passed
            if (isset($data[self::PROPERTY_EVENT_CODE]) &&
                !in_array($data[self::PROPERTY_EVENT_CODE], $eventCodes->getConstants())) {
                $invalid[self::PROPERTY_EVENT_CODE] = $data[self::PROPERTY_EVENT_CODE];
            }
        }

        if (!empty($missing)) {
            throw new InvalidDataException('Field(s) missing from notification data: ' . join(', ', $missing));
        }

        if (!empty($invalid)) {
            throw new InvalidDataException('Invalid value for the field(s) with key(s): ' . join(', ', $invalid));
        }
    }
}
