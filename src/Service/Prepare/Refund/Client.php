<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/13
 * Time: 17:12
 */

namespace Lmh\Fuiou\Service\Prepare\Refund;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Lmh\Fuiou\Exceptions\FuiouPayException;
use Lmh\Fuiou\Exceptions\HttpException;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Service\Prepare\BaseClient;

class Client extends BaseClient
{
    /**
     * 订单查询接口
     * @param array $params
     * @return Collection
     * @throws FuiouPayException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function create(array $params): Collection
    {
        $url = $this->getApi('/aggregatePay/commonRefund');
        if (empty($params['term_id'])) {
            $params['term_id'] = self::TERM_ID;
        }
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $params['sign_joint'] = [
            'mchnt_cd', 'order_type', 'mchnt_order_no', 'refund_order_no', 'total_amt', 'refund_amt', 'term_id', 'random_str', 'version', 'mchnt_key'
        ];
        $response = $this->request($url, $params, 'POST');
        $this->checkResult($response);
        return $response;
    }
}