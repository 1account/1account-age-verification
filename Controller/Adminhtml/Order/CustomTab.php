<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Psr\Log\LoggerInterface;

class CustomTab extends Order
{
    /**
     * @var OrderInterfaceFactory
     */
    protected $orderInterfaceFactory;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param InlineInterface $translateInline
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param OrderInterfaceFactory $orderInterfaceFactory
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        Context                  $context,
        Registry                 $coreRegistry,
        FileFactory              $fileFactory,
        InlineInterface          $translateInline,
        PageFactory              $resultPageFactory,
        JsonFactory              $resultJsonFactory,
        LayoutFactory            $resultLayoutFactory,
        RawFactory               $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface          $logger,
        OrderInterfaceFactory    $orderInterfaceFactory,
        RedirectFactory          $redirectFactory
    ) {
        $this->orderInterfaceFactory = $orderInterfaceFactory;
        $this->redirectFactory = $redirectFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * Add order av status action
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $order = $this->orderInterfaceFactory->create()->load($this->getRequest()->getParam('order_id'));
        $order->setData('order_av', $this->getRequest()->getParam('av_status'));
        $this->orderRepository->save($order);
    }
}
