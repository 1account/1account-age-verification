<?php

namespace OneAccount\OneAccountAgeVerification\Controller\Validate;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class NotificationValidation extends Action
{
    const ONEACCOUNT_ORDER_DATA_UPDATE_URL = 'https://stage-api.1account.net/platforms/woomagento/incompleteAV/update';

    public function execute()
    {
        $validateData = curl_init(self::ONEACCOUNT_ORDER_DATA_UPDATE_URL);

        $orderData = [
            'orderId' => $this->getRequest()->getParam('id'),
            'status' => 'AVSUCCESS',
            'platformId' => 'MAGENTO'
        ];

        curl_setopt($validateData, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($validateData, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($validateData, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($validateData, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        $response = json_decode(curl_exec($validateData), true);
    }
}
