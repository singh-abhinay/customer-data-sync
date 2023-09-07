<?php

namespace Abhinay\Custom\Observer;

/**
 * Class NewCustomerRegistration
 * @package Abhinay\Custom\Observer
 */
class NewCustomerRegistration implements \Magento\Framework\Event\ObserverInterface
{
    const STATUS_TRUE = 'true';

    const REQUEST_TYPE = 'create';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Abhinay\Custom\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * NewCustomerRegistration constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Abhinay\Custom\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Abhinay\Custom\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_request = $request;
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $postParams = (array)$this->getPostParams();
        if ((!empty($postParams)) && (!empty($customer->getEmail()))) {
            $postParams['type'] = self::REQUEST_TYPE;
            $response = $this->_helper->sendCustomerData($postParams);
            if ((!empty($response)) && ($response['status'] != self::STATUS_TRUE)) {
                $this->_logger->error('Customer data is not syncing for the customer email ' . $postParams['email']);
            }
        }
    }

    /**
     * Getting customer registration post data
     * @return mixed
     */
    public function getPostParams()
    {
        return $this->_request->getPost();
    }
}
