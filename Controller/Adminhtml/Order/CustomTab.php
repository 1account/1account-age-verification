<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Adminhtml\Order;

use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Controller\Adminhtml\Order;

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
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        InlineInterface $translateInline,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        OrderInterfaceFactory $orderInterfaceFactory,
        RedirectFactory $redirectFactory
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
     * @return ResultInterface
     */
    public function execute()
    {
        $order = $this->orderInterfaceFactory->create()->load($this->getRequest()->getParam('order_id'));
        $order->setData('order_av', $this->getRequest()->getParam('av_status'));
        $order->save();
//        $message = __("1Account order status updated");
//        $this->messageManager->addSuccessMessage($message);

        return $order;
    }
}
