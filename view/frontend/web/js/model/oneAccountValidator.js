define(
    [
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'mage/cookies'
    ],
    function ($t, messageList, $, quote, checkout) {
        'use strict';

        var client_logged_in = window.checkoutConfig.isCustomerLoggedIn;

        if (client_logged_in === true) {
            if (window.checkoutConfig.customerData.custom_attributes) {
                if (window.checkoutConfig.customerData.custom_attributes.av_status) {
                    var client_av_status = window.checkoutConfig.customerData.custom_attributes.av_status.value;
                }
            }
        }

        var authCode = window.checkoutConfig.oneaccount.authCode;
        var avLevel = window.checkoutConfig.oneaccount.avLevel;
        var clientId = window.checkoutConfig.oneaccount.clientId;
        var moduleEnable = window.checkoutConfig.oneaccount.moduleEnable;

        var isValid;

        function getIsValidState()
        {
            isValid = $.cookie("isValid");
            return isValid;
        }

        function setIsValidState(valid)
        {
            return $.cookie("isValid", valid);
        }

        const oneAccountValidate = () => {
            if ( client_logged_in === true ) {
                if (client_av_status !== 'success') {
                    showModal();
                } else {
                    $(".payment-method-content button").trigger("click")
                }
            } else {
                if (getIsValidState() === null || getIsValidState() === 'false') {
                    showModal();
                } else {
                    $(".payment-method-content button").trigger("click")
                }
            }
        }
        if ((client_logged_in === true && (client_av_status !== 'success')) || client_logged_in === false ) {
            PUSH_API.init({
                authCode: authCode,
                avLevel: avLevel,
                clientId: clientId,
                onComplete: (response) => {
                    if (response.status === "AV_SUCCESS") {
                        messageList.addSuccessMessage({ message: $t('One Account validation passed! You can continue place order') });
                        if ( client_logged_in === true ) {
                            $.ajax({
                                url: "order/statusupdate",
                                data: {
                                    'id': window.checkoutConfig.customerData.id,
                                    'status': 'success'
                                },
                                type: "GET",
                                dataType: 'json',
                            });
                            client_av_status = 'success';
                        } else {
                            setIsValidState('true');
                        }
                    } else {
                        messageList.addErrorMessage({ message: $t('One Account validation failed') });
                        if ( client_logged_in === true ) {
                            $.ajax({
                                url: "order/statusupdate",
                                data: {
                                    'id': window.checkoutConfig.customerData.id,
                                    'status': 'failed'
                                },
                                type: "GET",
                                dataType: 'json',
                            });
                            client_av_status = 'failed';
                        } else {
                            setIsValidState('false');
                        }
                    }
                    oneAccountValidate();
                }
            });
        }

        function showModal()
        {
            var shippingAddress = quote.shippingAddress();

            PUSH_API.validate({
                msisdn: shippingAddress.telephone,
                email: checkout.getValidatedEmailValue(),
                forename: shippingAddress.firstname,
                surname: shippingAddress.lastname,
                country: shippingAddress.countryId,
                city: shippingAddress.city,
                street: shippingAddress.street[0],
                building: shippingAddress.home,
                postCode: shippingAddress.postcode
            });

            e.preventDefault();
        }

        return {
            validate: function () {
                if (moduleEnable === '1') {
                    if (client_logged_in === true) {
                        if (client_av_status !== 'success') {
                            oneAccountValidate();
                        } else {
                            return true;
                        }
                    } else {
                        if (getIsValidState() === null || getIsValidState() === "false") {
                            oneAccountValidate();
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
    }
);
