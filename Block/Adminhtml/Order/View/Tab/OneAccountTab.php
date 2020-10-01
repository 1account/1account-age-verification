<?php

namespace OneAccount\OneAccountAgeVerification\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Model\Order;
use OneAccount\OneAccountAgeVerification\Model\Attribute\Source\AvValidation;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\LocalizedException;

class OneAccountTab extends Template implements TabInterface
{
    protected $_template = 'order/view/tab/oneaccounttab.phtml';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var AvValidation
     */
    private $avValidationStatuses;

    /**
     * @var Manager
     */
    private $eventManager;

    /**
     * View constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AvValidation $avValidationStatuses
     * @param Manager $eventManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AvValidation $avValidationStatuses,
        Manager $eventManager,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->avValidationStatuses = $avValidationStatuses;
        $this->eventManager = $eventManager;
        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order_av_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            ['label' => __('Submit Av Status'), 'class' => 'action-save action-secondary', 'onclick' => $onclick]
        );
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('customtab/*/customTab', ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * Retrieve order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * @return array
     */
    public function getAvStatuses()
    {
        $result = [];
        $statuses = $this->avValidationStatuses->getAllOptions();
        foreach ($statuses as $status) {
            $label = $status['label']->getText();
            $result = array_merge($result, [$label => $status['value']]);
        }

        return $result;
    }

    /**
     * Retrieve order model instance
     *
     * @return int
     *Get current id order
     */
    public function getOrderId()
    {
        return $this->getOrder()->getEntityId();
    }

    /**
     * Retrieve order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('1Account Status');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('1Account Status');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
