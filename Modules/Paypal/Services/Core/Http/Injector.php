<?php

/**
 * @package PayPalCheckoutSdk/Core
 */

namespace Modules\Paypal\Services\Core\Http;

interface Injector
{
    public function inject($httpRequest);
}
