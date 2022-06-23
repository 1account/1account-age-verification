<?php

namespace OneAccount\OneAccountAgeVerification\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class OrderPlaceAfter implements ObserverInterface
{
    public const ONEACCOUNT_ORDER_DATA_STORE_URL = 'https://api.1account.net/platforms/woomagento/incompleteAV/create';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface     $scopeConfig,
        OrderRepositoryInterface $orderRepositoryInterface,
        StoreManagerInterface    $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->storeManager = $storeManager;
    }

    /**
     * Observer execute
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');
        if ($this->scopeConfig->getValue(ConfigObserver::MODULE_ENABLE_PATH_URL) === '1') {
            if ($order->getData('order_av') === 'not_validate') {
                $validateData = curl_init(self::ONEACCOUNT_ORDER_DATA_STORE_URL);
                $items = [];
                foreach ($order->getItems() as $item) {
                    $items[] = [
                        'itemName' => $item->getName(),
                        'itemDescription' => $item->getDescription(),
                    ];
                }
                $orderData = [
                    'orderId' => $order->getId(),
                    'platformId' => 'MAGENTO',
                    'userEmail' => $order->getCustomerEmail(),
                    'userPhone' => $order->getShippingAddress() ? $order->getShippingAddress()->getTelephone() : '',
                    'items' => $items,
                    'avUrl' =>
                        $order->getStore()->getBaseUrl() . 'status/validate?key=' . base64_encode($order->getId()),
                    'storeUrl' => $order->getStore()->getBaseUrl(),
                    'customerName' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                ];

                curl_setopt($validateData, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($validateData, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($validateData, CURLOPT_POSTFIELDS, json_encode($orderData));
                curl_setopt($validateData, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

                curl_exec($validateData);
            }
        }
    }
}
