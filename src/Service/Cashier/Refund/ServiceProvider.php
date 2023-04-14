<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/13
 * Time: 17:12
 */

namespace Lmh\Fuiou\Service\Cashier\Refund;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     * @author lmh
     */
    public function register(Container $pimple)
    {
        $pimple['refund'] = function ($pimple) {
            return new Client($pimple);
        };
    }
}