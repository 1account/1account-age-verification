<?php
namespace OneAccount\OneAccountAgeVerification\Block;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class StatusPage extends Template
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * StatusPage constructor.
     * @param Context $context
     * @param Http $request
     */
    public function __construct(
        Context $context,
        Http $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    public function getMessage()
    {
        $status = $this->request->getParam('status');

        if ($status === 'valid') {
            return 'Thank you. You have successfully verified your age.';
        }

        return 'We are sorry we have been unable to verify your age based on the details you have submitted.';
    }
}
