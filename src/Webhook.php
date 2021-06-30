<?php

namespace Adyen\Webhook;

interface Webhook
{
    /**
     * @return WebhookResponse
     */
    public function process();
}
