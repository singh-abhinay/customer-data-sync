<?php

namespace Abhinay\Custom\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CustomerInfoUpdate
 * @package Abhinay\Custom\Observer
 */
class CustomerInfoUpdate implements \Magento\Framework\Event\ObserverInterface
{
    const STATUS_TRUE = 'true';

    const REQUEST_TYPE = 'update';
    /**
     * @var \Abhinay\Custom\Helper\Data
     */
    protected $_helper;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * CustomerInfoUpdate constructor.
     * @param \Abhinay\Custom\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Abhinay\Custom\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $email = $observer->getEvent()->getEmail();
        if (!empty($email)) {
            $customerData = $this->customerRepository->get($email);
            $postParams = [];
            $postParams['email'] = $email;
            $postParams['firstname'] = $customerData->getFirstname();
            $postParams['lastname'] = $customerData->getLastname();
            $postParams['type'] = self::REQUEST_TYPE;
            $response = $this->_helper->sendCustomerData($postParams);
            if ((!empty($response)) && ($response['status'] != self::STATUS_TRUE)) {
                $this->_logger->error('Customer data is not syncing for the customer email ' . $email);
            }
        }
    }
}
