<?php

/**
 * @package PayPalCheckoutSdk/Core
 */

namespace Modules\Paypal\Services\Core\Http;

class HttpRequest
{
    public $path;
    public $verb;
    public $headers = [];
    public $body;

    public function __construct($path, $verb)
    {
        $this->path = $path;
        $this->verb = $verb;
    }
}
