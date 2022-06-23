<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Validate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class NotificationValidation extends Action
{
    public const ONEACCOUNT_ORDER_DATA_UPDATE_URL = 'https://api.1account.net/platforms/woomagento/incompleteAV/update';

    /**
     * Notification validate action
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $validateData = curl_init(self::ONEACCOUNT_ORDER_DATA_UPDATE_URL);
        $orderData = [
            'orderId' => $this->getRequest()->getParam('id'),
            'status' => 'AVSUCCESS',
            'platformId' => 'MAGENTO',
        ];

        curl_setopt($validateData, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($validateData, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($validateData, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($validateData, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_exec($validateData);
    }
}
