<?php

namespace Lmh\Fuiou\Service\Wap\Notify;

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
        $pimple['notify'] = function ($pimple) {
            return new Client($pimple);
        };
    }
}