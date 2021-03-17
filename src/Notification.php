<?php

namespace Adyen\Notification;

interface Notification
{
    /**
     * @return NotificationResponse
     */
    public function process();
}
