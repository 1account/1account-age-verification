<?php
namespace OneAccount\OneAccountAgeVerification\Block;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template\Context;
use \Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use OneAccount\OneAccountAgeVerification\Observer\ConfigObserver;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template;

class StatusValidate extends Template
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * StatusValidate constructor.
     * @param Context $context
     * @param Http $request
     * @param OrderRepository $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Http $request,
        OrderRepository $orderRepository,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getOrderData()
    {
        $orderId = base64_decode($this->request->getParam('key'));

        $order = $this->orderRepository->get($orderId);
        $building = null;
        $street = null;
        if (array_key_exists('0', $order->getShippingAddress()->getStreet())) {
            $street = $order->getShippingAddress()->getStreet()[0];
        }
        if (array_key_exists('1', $order->getShippingAddress()->getStreet())) {
            $building = $order->getShippingAddress()->getStreet()[1];
        }

        $data = [
            'orderId' => $orderId,
            'msisdn' => $order->getShippingAddress()->getTelephone(),
            'email' => $order->getCustomerEmail(),
            'forename' => $order->getShippingAddress()->getFirstname(),
            'surname' => $order->getShippingAddress()->getLastname(),
            'country' => $order->getShippingAddress()->getCountryId(),
            'city' => $order->getShippingAddress()->getCity(),
            'street' => $street,
            'building' => $building,
            'postCode' => $order->getShippingAddress()->getPostcode(),
        ];

        return $data;
    }

    public function checkCustomerExist()
    {
        $orderId = base64_decode($this->request->getParam('key'));

        $order = $this->orderRepository->get($orderId);


        if ($order->getCustomerId()) {
            return true;
        }

        return false;
    }

    public function getCustometId()
    {
        $orderId = base64_decode($this->request->getParam('key'));

        $order = $this->orderRepository->get($orderId);

        if ($order->getCustomerId()) {
            return $order->getCustomerId();
        }

        return null;
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getAuthCode()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue(ConfigObserver::CLIENT_SECRET_PATH_URL));
    }

    public function getAvLevel()
    {
        return $this->scopeConfig->getValue(ConfigObserver::CLIENT_AVLEVEL_PATH_URL);
    }

    public function getClientId()
    {
        return $this->scopeConfig->getValue(ConfigObserver::CLIENT_ID_PATH_URL);
    }

    public function getModuleEnable()
    {
        return $this->scopeConfig->getValue(ConfigObserver::MODULE_ENABLE_PATH_URL);
    }
}
