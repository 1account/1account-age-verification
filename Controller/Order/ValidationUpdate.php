<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Order;

use Magento\Framework\App\Action\Action;
use \Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ValidationUpdate extends Action
{
    protected $resultJsonFactory;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * ValidationUpdate constructor.
     * @param Context $context
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $order = $this->orderRepository->get($this->getRequest()->getParam('id'));
            $order->setData('order_av', $this->getRequest()->getParam('status'));
            $order->save();
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
        }
    }
}
