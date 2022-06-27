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
        var clientSessionAvChecked = false;
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

        if ((client_logged_in === true && (client_av_status !== 'success')) || client_logged_in === false) {
            PUSH_API.init({
                authCode: authCode,
                avLevel: avLevel,
                clientId: clientId,
                onComplete: function (response) {
                    if (response.status === "AV_SUCCESS") {
                        alert('Thank you. You have successfully verified your age.');
                        if (client_logged_in === true) {
                            $.ajax({
                                url: "/status/order/statusupdate",
                                data: {
                                    'id': window.checkoutConfig.customerData.id,
                                    'status': 'success'
                                },
                                type: "GET",
                                dataType: 'json'
                            });
                            client_av_status = 'success';
                        } else {
                            setIsValidState('true');
                        }
                    } else if (response.status === "AV_FAILED") {
                        alert('We are sorry we have been unable to verify your age based on the details you have submitted.');
                        if (client_logged_in === true) {
                            $.ajax({
                                url: "/status/order/statusupdate",
                                data: {
                                    'id': window.checkoutConfig.customerData.id,
                                    'status': 'failed'
                                },
                                type: "GET",
                                dataType: 'json'
                            });
                            client_av_status = 'failed';
                        } else {
                            setIsValidState('false');
                        }
                    }
                    clientSessionAvChecked = true;
                    $(".payment-method-content button").trigger("click")
                },
                onClose: function () {
                    clientSessionAvChecked = true;
                    $(".payment-method-content button").trigger("click")
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
                building: shippingAddress.street[1],
                postCode: shippingAddress.postcode
            });
        }

        return {
            validate: function () {
                if (moduleEnable === '1') {
                    if (client_logged_in === true) {
                        if (clientSessionAvChecked === false && client_av_status !== 'success') {
                            showModal();
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if (clientSessionAvChecked === false && getIsValidState() === null) {
                            showModal();
                            return false;
                        } else if (getIsValidState() === 'false') {
                            $.cookie("isValid", null);
                            return true;
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
    }
);
