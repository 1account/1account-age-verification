<?php

namespace OneAccount\OneAccountAgeVerification\Block\Validator;

use OneAccount\OneAccountAgeVerification\Observer\ConfigObserver;
use Magento\Framework\View\Element\Template;

class CustomCheckoutValidator extends Template
{

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getClientLogo()
    {
        $logo = $this->_scopeConfig->getValue(ConfigObserver::CLIENT_LOGO_PATH_URL);
        return (!empty($logo)) ? $logo : '';
    }
}
