define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'OneAccount_OneAccountAgeVerification/js/model/oneAccountValidator'
    ],
    function (Component, additionalValidators, oneAccountValidator) {
        'use strict';
        additionalValidators.registerValidator(oneAccountValidator);
        return Component.extend({});
    }
);
