<?php

namespace Lmh\Fuiou\Service\Prepare\Transaction;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     * @return void
     */
    public function register(Container $pimple)
    {
        $pimple['transaction'] = function ($pimple) {
            return new Client($pimple);
        };
    }
}