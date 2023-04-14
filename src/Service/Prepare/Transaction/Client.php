<?php

namespace Lmh\Fuiou\Service\Prepare\Transaction;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Lmh\Fuiou\Constant\OrderType;
use Lmh\Fuiou\Constant\TradeType;
use Lmh\Fuiou\Exceptions\FuiouPayException;
use Lmh\Fuiou\Exceptions\HttpException;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Service\Prepare\BaseClient;
use function Lmh\Fuiou\get_client_ip;

class Client extends BaseClient
{
    /**
     * 统一下单请求
     * 商户展示二维码给用户，用户使用微信、支付宝等扫码支付
     * @param array $params
     * @return Collection
     * @throws FuiouPayException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function native(array $params): Collection
    {
        $url = $this->getApi('/aggregatePay/preCreate');
        // 终端号
        if (empty($params['term_id'])) {
            $params['term_id'] = self::TERM_ID;
        }
        if (empty($params['term_ip'])) {
            $params['term_ip'] = get_client_ip();
        }
        // 下单时间
        if (empty($params['txn_begin_ts'])) {
            $params['txn_begin_ts'] = date('YmdHis', time());
        }
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $params['sign_joint'] = [
            'mchnt_cd', 'order_type', 'order_amt', 'mchnt_order_no', 'txn_begin_ts', 'goods_des', 'term_id',
            'term_ip', 'notify_url', 'random_str', 'version', 'mchnt_key'
        ];
        $params['order_type'] = $params['order_type'] ?? OrderType::WECHAT;
        // 请求参数
        if ($params['order_type'] == OrderType::WECHAT) {
            $params['sub_appid'] = $this->config->get('wechat_appid');
        }
        $response = $this->request($url, $params, 'POST');
        $this->checkResult($response);
        return $response;
    }

    /**
     * 公众号/服务窗统一下单接口
     * @param array $params
     * @return Collection
     * @throws FuiouPayException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function jsapi(array $params): Collection
    {
        $url = $this->getApi('/aggregatePay/wxPreCreate');
        // 下单时间
        if (empty($params['txn_begin_ts'])) {
            $params['txn_begin_ts'] = date('YmdHis', time());
        }
        // 终端号
        if (empty($params['term_id'])) {
            $params['term_id'] = self::TERM_ID;
        }
        if (empty($params['term_ip'])) {
            $params['term_ip'] = get_client_ip();
        }
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $params['sign_joint'] = [
            'mchnt_cd', 'trade_type', 'order_amt', 'mchnt_order_no', 'txn_begin_ts', 'goods_des', 'term_id', 'term_ip', 'notify_url', 'random_str', 'version', 'mchnt_key'
        ];
        $tradeType = $params['trade_type'] ?? TradeType::JSAPI;
        // 请求参数
        if ($tradeType == TradeType::JSAPI) {
            $params['sub_appid'] = $this->config->get('sub_appid');
        }
        $response = $this->request($url, $params, 'POST');
        $this->checkResult($response);
        return $response;
    }


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
    public function query(array $params): Collection
    {
        $url = $this->getApi('/aggregatePay/commonQuery');
        if (empty($params['term_id'])) {
            $params['term_id'] = self::TERM_ID;
        }
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $params['sign_joint'] = [
            'mchnt_cd', 'order_type', 'mchnt_order_no', 'term_id', 'random_str', 'version', 'mchnt_key'
        ];
        $response = $this->request($url, $params, 'POST');
        $this->checkResult($response);
        return $response;
    }
}