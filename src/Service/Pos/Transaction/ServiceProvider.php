<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/18
 * Time: 15:26
 */

namespace Lmh\Fuiou\Service\Pos\Transaction;

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