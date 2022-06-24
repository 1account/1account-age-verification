<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Throwable;

class ValidationUpdate extends Action
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context         $context,
        OrderRepository $orderRepository,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Validation update action
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        try {
            $order = $this->orderRepository->get($this->getRequest()->getParam('id'));
            $order->setData('order_av', $this->getRequest()->getParam('status'));
            $this->orderRepository->save($order);
        } catch (Throwable $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }
    }
}
