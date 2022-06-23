<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Order;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class StatusUpdate extends Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerInterfaceFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param CustomerRepositoryInterface $customerInterfaceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                     $context,
        CustomerRepositoryInterface $customerInterfaceFactory,
        LoggerInterface             $logger
    ) {
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Status update action
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        try {
            $customer = $this->customerInterfaceFactory->getById($this->getRequest()->getParam('id'));
            $customer->setCustomAttribute('av_status', $this->getRequest()->getParam('status'));
            $this->customerInterfaceFactory->save($customer);
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }
    }
}
