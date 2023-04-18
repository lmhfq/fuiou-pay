<?php

namespace Lmh\Fuiou\Service\Cashier;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Lmh\Fuiou\Constant\RespCode;
use Lmh\Fuiou\Exceptions\FuiouPayException;
use Lmh\Fuiou\Exceptions\HttpException;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Support\Config;
use Lmh\Fuiou\Support\Http;
use Lmh\Fuiou\Support\RsaUtil;
use Lmh\Fuiou\Support\ServiceContainer;
use Lmh\Fuiou\Support\Signer;
use Lmh\Fuiou\Traits\ResponseCastable;


/**
 *
 */
class BaseClient
{
    use ResponseCastable;

    /**
     * API版本
     */
    public const API_VERSION = '1.0.0';
    /**
     * 测试环境API地址
     */
    public $TRANSACTION_API_HOST_DEV = 'https://aggpc-test.fuioupay.com';
    public $REFUND_API_HOST_DEV = 'https://refund-transfer-test.fuioupay.com';

    /**
     * 正式环境API地址
     */
    public $TRANSACTION_API_HOST = 'https://aggpc.fuioupay.com';
    public $REFUND_API_HOST = 'https://refund-transfer.fuioupay.com';

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var
     */
    protected $http;

    /**
     */
    public function __construct(ServiceContainer $container)
    {
        $config = $container['config'] ?? [];
        $this->config = $config;
    }

    /**
     * @param string $api
     * @param array $params
     * @param string $method
     * @return Collection
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function request(string $api, array $params, string $method = 'post'): Collection
    {
        $message = json_encode($params);
        //明文json字符串进行加密
        $message = RsaUtil::publicEncrypt($message, $this->config->get('fuiou_public_key'));
        $body = [
            'message' => $message,
            'mchnt_cd' => $this->config->get('mchnt_cd'),
        ];
        $options = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($body)
        ];
        $response = $this->getHttp()->request($api, $method, $options);
        if ($response->getStatusCode() !== 200) {
            throw new HttpException('[富友支付异常]请求异常: HTTP状态码 ' . $response->getStatusCode());
        }
        return $this->castResponse($response);
    }

    /**
     * @return array 解除参数
     */
    public function baseParams(): array
    {
        // 加载配置数据
        return array_merge(
            $this->config->only(['mchnt_cd'])->toArray(),
            [
                'ver' => self::API_VERSION,
            ]
        );
    }

    /**
     * 请求客户端
     *
     * @return Http
     */
    public function getHttp(): Http
    {
        if (is_null($this->http)) {
            $this->http = new Http($this->config->get('http'));
        }
        return $this->http;
    }

    /**
     * 获取API地址
     *
     * @param string $api
     * @return string
     */
    public function getApi(string $api, $type): string
    {
        $constantType = strtoupper($type) . '_API_HOST';
        if ($this->config->get('debug')) {
            $constantType .= '_DEV';
        }
        return $this->{$constantType} . $api;
    }

    /**
     * @throws FuiouPayException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function checkResult(Collection $response)
    {
        //验证
        if (isset($response['resp_code']) && RespCode::SUCCESS === $response['resp_code']) {
            //得到响应，解密数据
            $decrypted = RsaUtil::privateDecrypt($response->get("message"), $this->config->get('private_key'));
            $response->offsetSet('message_data', json_decode($decrypted, true));
            return;
        }
        $message = $response['resp_desc'] ?? '系统错误';
        $code = $response['resp_code'] ?? '';
        throw new FuiouPayException('[支付异常]异常代码：' . $code . ' 异常信息：' . $message, $code);
    }
}