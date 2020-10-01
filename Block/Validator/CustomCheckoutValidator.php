<?php

namespace OneAccount\OneAccountAgeVerification\Block\Validator;

use OneAccount\OneAccountAgeVerification\Observer\ConfigObserver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

class CustomCheckoutValidator extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CustomCheckoutValidator constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getClientLogo()
    {
        $logo = $this->scopeConfig->getValue(ConfigObserver::CLIENT_LOGO_PATH_URL);
        return (!empty($logo)) ? $logo : '';
    }
}
