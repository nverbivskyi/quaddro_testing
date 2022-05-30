<?php

namespace Test\CustomerAvatar\Model;

use Test\CustomerAvatar\Logger\Logger;
use Magento\Framework\HTTP\Adapter\CurlFactory;

class Api
{
    const CUSTOMER_URL = 'https://avatars.abstractapi.com/v1/';
    const CUSTOMER_KEY = '59501e9ee78240d9b6441c6f3bbc2d3d';
    const CUSTOMER_NAME = 'Test';

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    /**
     * @param CurlFactory $curl_factory
     * @param Logger $logger
     */
    public function __construct(CurlFactory $curl_factory, Logger $logger)
    {
        $this->curlFactory = $curl_factory;
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return array|string|null
     */
    public function getRandomImage()
    {
        $imageJson = $this->loadFromApi();

        if (!isset($imageJson)) {
            return null;
        }

        return $imageJson;
    }

//    /**
//     * @param string $image_url
//     * @return bool
//     */
//    public function UrlSuccess(string $image_url = ""): bool
//    {
//        $result = $this->requestCurl($image_url);
//        $code   = \Zend_Http_Response::extractCode($result);
//        $body   = \Zend_Http_Response::extractBody($result);
//        if ($code != 200) {
//            $this->getLogger()->log(
//                'notice',
//                'Invalid avatar url',
//                ['url' => $image_url, 'response' => $body]
//            );
//        }
//        return ($code == 200);
//    }

    /**
     * @return array|string
     */
    public function loadFromApi()
    {
        $url   = self::CUSTOMER_URL;
        $query = [
            'api_key' => self::CUSTOMER_KEY,
            'name' => self::CUSTOMER_NAME
        ];
        $dynamic_url = $url . '?' . http_build_query($query);
        $result      = $this->requestCurl($dynamic_url);
        $code        = \Zend_Http_Response::extractCode($result);
        $body        = \Zend_Http_Response::extractBody($result);
        $base64Body = base64_encode(\Zend_Http_Response::extractBody($result));

        if ($code != 200) {
            $this->getLogger()->log('notice', 'API response failure', ['body' => $body]);
            return [];
        }

        return 'data:image/png;base64,' . $base64Body;
    }

    /**
     * @param string $urlApi
     * @return string
     */
    private function requestCurl(string $urlApi): string
    {
        $httpAdapter = $this->curlFactory->create();
        $httpAdapter->write(
            \Zend_Http_Client::GET,
            $urlApi,
            '1.1',
            ["Content-Type:application/json"]
        );

        return $httpAdapter->read();
    }
}
