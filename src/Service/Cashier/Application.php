<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/14
 * Time: 10:25
 */

namespace Lmh\Fuiou\Service\Cashier;

use Lmh\Fuiou\Support\ServiceContainer;

/**
 * @property Transaction\Client $transaction
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Transaction\ServiceProvider::class,
    ];
}