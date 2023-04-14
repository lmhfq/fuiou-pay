<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/14
 * Time: 10:26
 */

namespace Lmh\Fuiou\Service\Cashier\Transaction;


use Illuminate\Support\Collection;
use Lmh\Fuiou\Constant\PayType;
use Lmh\Fuiou\Service\Cashier\BaseClient;

class Client extends BaseClient
{

    /**
     * @author lmh
     */
    public function pay(array $params): Collection
    {
        $url = $this->getApi('/order.fuiou');
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $params['sign_joint'] = [
            'mchnt_cd', 'trade_type', 'order_amt', 'mchnt_order_no', 'txn_begin_ts', 'goods_des', 'term_id', 'term_ip', 'notify_url', 'random_str', 'version', 'mchnt_key'
        ];
        $params['pay_type'] = $params['pay_type'] ?? PayType::ALI_PAY_JL;

        $response = $this->request($url, $params, 'POST');

        $this->checkResult($response);
        return $response;

    }
}