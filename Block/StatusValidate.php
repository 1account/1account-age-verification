<?php

namespace OneAccount\OneAccountAgeVerification\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use OneAccount\OneAccountAgeVerification\Observer\ConfigObserver;

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
     * @param Context $context
     * @param Http $request
     * @param OrderRepository $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context               $context,
        Http                  $request,
        OrderRepository       $orderRepository,
        ScopeConfigInterface  $scopeConfig,
        EncryptorInterface    $encryptor,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get order data
     *
     * @return array
     * @throws InputException
     * @throws NoSuchEntityException
     */
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

        return [
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
    }

    /**
     * Check customer exist
     *
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function checkCustomerExist()
    {
        $orderId = base64_decode($this->request->getParam('key'));
        $order = $this->orderRepository->get($orderId);
        if ($order->getCustomerId()) {
            return true;
        }

        return false;
    }

    /**
     * Get customer id
     *
     * @return int|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getCustomerId()
    {
        $orderId = base64_decode($this->request->getParam('key'));
        $order = $this->orderRepository->get($orderId);
        if ($order->getCustomerId()) {
            return $order->getCustomerId();
        }

        return null;
    }

    /**
     * Get store base url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get auth code
     *
     * @return string
     */
    public function getAuthCode()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue(ConfigObserver::CLIENT_SECRET_PATH_URL));
    }

    /**
     * Get av level
     *
     * @return mixed
     */
    public function getAvLevel()
    {
        return $this->scopeConfig->getValue(ConfigObserver::CLIENT_AVLEVEL_PATH_URL);
    }

    /**
     * Get client id
     *
     * @return mixed
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue(ConfigObserver::CLIENT_ID_PATH_URL);
    }

    /**
     * Is module enabled
     *
     * @return mixed
     */
    public function getModuleEnable()
    {
        return $this->scopeConfig->getValue(ConfigObserver::MODULE_ENABLE_PATH_URL);
    }
}
