<?php

namespace Abhinay\Custom\Controller\Adminhtml\Customer;

/**
 * Class DataSync
 * @package Abhinay\Custom\Controller\Adminhtml\Customer
 */
class DataSync extends \Magento\Backend\App\Action
{
    const STATUS_FALSE = 'false';

    const REQUEST_TYPE = 'update';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var \Abhinay\Custom\Helper\Data
     */
    protected $helper;

    /**
     * DataSync constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Abhinay\Custom\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Abhinay\Custom\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $itemsSync = 0;
            foreach ($collection as $item) {
                $data = [];
                $data['firstname'] = $item->getFirstname();
                $data['lastname'] = $item->getLastname();
                $data['email'] = $item->getEmail();
                $data['type'] = self::REQUEST_TYPE;
                $response = $this->helper->sendCustomerData($data);
                if ((!empty($response)) && ($response['status'] != self::STATUS_FALSE)) {
                    $itemsSync++;
                }
            }
            $this->messageManager->addSuccess('A total of %1 data(s) were sync successfully.', $itemsSync);
        } catch (Exception $e) {
            $this->messageManager->addError('Something went wrong while syncing the data.' . $e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('customer/index/index');
    }
}
