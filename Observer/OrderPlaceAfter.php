<?php

namespace OneAccount\OneAccountAgeVerification\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\CookieManagerInterface;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * OrderPlaceAfter constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepositoryInterface,
        CookieManagerInterface $cookieManager
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param EventObserver $observer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');
        if ($this->scopeConfig->getValue(ConfigObserver::MODULE_ENABLE_PATH_URL) === '1') {
            if ($order->getCustomerIsGuest() === 0) {
                $customer = $this->customerRepositoryInterface->get($order->getCustomerEmail());
                if ($customer->getCustomAttribute('av_status')->getValue() === 'success') {
                    $order->setData('order_av', 'valid');
                    $this->orderRepositoryInterface->save($order);
                } else {
                    $order->setData('order_av', 'invalid');
                    $this->orderRepositoryInterface->save($order);
                }
            } else {
                if ($this->cookieManager->getCookie('isValid') === 'true') {
                    $order->setData('order_av', 'valid');
                    $this->orderRepositoryInterface->save($order);
                } else {
                    $order->setData('order_av', 'invalid');
                    $this->orderRepositoryInterface->save($order);
                }
            }
        }
    }
}
