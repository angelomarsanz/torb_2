<?php

/**
 * @package PayPalCheckoutSdk/Core
 */

namespace Modules\Paypal\Services\Core\Http;

class HttpException extends \Exception
{
    public $statusCode;
    public $headers;

    public function __construct($message = "", $statusCode = 0, $headers = [], \Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }
}
