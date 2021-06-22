<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Validate;

use Magento\Framework\App\Action\Action;

class Result extends Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
