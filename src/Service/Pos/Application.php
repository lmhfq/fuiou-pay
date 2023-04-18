<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/14
 * Time: 10:25
 */

namespace Lmh\Fuiou\Service\Pos;

use Lmh\Fuiou\Support\ServiceContainer;

/**
 * 富友-互联网扫码支付接口
 * @see http://180.168.100.158:13318/fuiouWposApipay/
 * @property Transaction\Client $transaction
 * @property Refund\Client $refund
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Transaction\ServiceProvider::class,
        Refund\ServiceProvider::class,
    ];
}