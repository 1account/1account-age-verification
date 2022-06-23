<?php

namespace OneAccount\OneAccountAgeVerification\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use OneAccount\OneAccountAgeVerification\Observer\ConfigObserver;

class CustomConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * CustomConfigProvider constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface   $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * Get Config
     *
     * @return array[]
     */
    public function getConfig()
    {
        return [
            'oneaccount' => [
                'authCode' => $this->encryptor->decrypt(
                    $this->scopeConfig->getValue(ConfigObserver::CLIENT_SECRET_PATH_URL)
                ),
                'avLevel' => $this->scopeConfig->getValue(ConfigObserver::CLIENT_AVLEVEL_PATH_URL),
                'clientId' => $this->scopeConfig->getValue(ConfigObserver::CLIENT_ID_PATH_URL),
                'moduleEnable' => $this->scopeConfig->getValue(ConfigObserver::MODULE_ENABLE_PATH_URL),
            ],
        ];
    }
}
