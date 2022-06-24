<?php

namespace OneAccount\OneAccountAgeVerification\Block;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class StatusPage extends Template
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * @param Context $context
     * @param Http $request
     */
    public function __construct(
        Context $context,
        Http    $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Get status message
     *
     * @return Phrase
     */
    public function getMessage()
    {
        $status = $this->request->getParam('status');

        if ($status === 'valid') {
            return __('Thank you. You have successfully verified your age.');
        }

        return __('We are sorry we have been unable to verify your age based on the details you have submitted.');
    }
}
