<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class StatusUpdate extends Action
{
    protected $resultJsonFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerInterfaceFactory;

    /**
     * StatusUpdate constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerInterfaceFactory
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerInterfaceFactory
    ) {
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $customer = $this->customerInterfaceFactory->getById($this->getRequest()->getParam('id'));
            $customer->setCustomAttribute('av_status', $this->getRequest()->getParam('status'));
            $this->customerInterfaceFactory->save($customer);
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
        }
    }
}
