<?php

namespace Lmh\Fuiou\Service\Prepare;

use Lmh\Fuiou\Support\ServiceContainer;

/**
 * @property Transaction\Client $transaction
 * @property Refund\Client $refund
 * @property Notify\Client $notify
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Transaction\ServiceProvider::class,
        Refund\ServiceProvider::class,
        Notify\ServiceProvider::class,
    ];
}