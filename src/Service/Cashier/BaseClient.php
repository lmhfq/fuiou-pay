<?php

namespace Lmh\Fuiou\Service\Cashier;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
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
    const API_VERSION = '3.0.0';
    /**
     * 测试环境API地址
     */
    const API_HOST_DEV = 'https://aggpc-test.fuioupay.com';

    /**
     * 正式环境API地址
     */
    const API_HOST_PRO = 'https://aggpc.fuioupay.com';

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var string
     */
    protected $notifyUrl;
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
        if ($config->get('environment') === 'dev') {
            $this->debug = true;
        }
        $this->config = $config;
    }

    /**
     * @param string $api
     * @param array $params
     * @param string $method
     * @return Collection
     * @throws GuzzleException
     * @throws HttpException
     * @author lmh
     */
    public function request(string $api, array $params, string $method = 'post'): Collection
    {
        $message = json_encode($params, JSON_UNESCAPED_UNICODE);
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
            'body' => json_encode($body, JSON_UNESCAPED_UNICODE)
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
    public function getApi(string $api): string
    {
        if ($this->debug) {
            return self::API_HOST_DEV . $api;
        } else {
            return self::API_HOST_PRO;
        }
    }
}