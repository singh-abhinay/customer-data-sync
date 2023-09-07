<?php

namespace Abhinay\Custom\Helper;

/**
 * Class Data
 * @package Abhinay\Custom\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const REQUEST_TYPE = 'update';
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Data constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_logger = $logger;
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Send Customer Data To Third Party API
     * @param $customerData
     * @return array|mixed
     */
    public function sendCustomerData($customerData)
    {
        $params = [
            'firstname' => $customerData['firstname'],
            'lastname' => $customerData['lastname'],
            'email' => $customerData['email'],
        ];
        if ($customerData['type'] != self::REQUEST_TYPE) {
            $url = $this->getApiUrlCreate();
        } else {
            $url = $this->getApiUrlUpdate();
        }
        $jsonData = json_encode($params);
        $this->curl->setOption(CURLOPT_HEADER, 0);
        $this->curl->setOption(CURLOPT_TIMEOUT, 60);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->post($url, $jsonData);
        $response = $this->curl->getBody();
        $returnResponse = json_decode($response, true);
        return $returnResponse;
    }

    /**
     * Getting API end points with URL for third-party create request
     * @return mixed
     */
    public function getApiUrlCreate()
    {
        return $this->scopeConfig->getValue(
            'abhinay/general/api_url_create',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
    }

    /**
     * Getting API end points with URL for third-party update request
     * @return mixed
     */
    public function getApiUrlUpdate()
    {
        return $this->scopeConfig->getValue(
            'abhinay/general/api_url_update',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
    }
}
