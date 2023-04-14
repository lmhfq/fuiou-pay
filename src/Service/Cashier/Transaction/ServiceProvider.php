<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/14
 * Time: 10:26
 */

namespace Lmh\Fuiou\Service\Cashier\Transaction;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['transaction'] = function ($pimple) {
            return new Client($pimple);
        };
    }
}