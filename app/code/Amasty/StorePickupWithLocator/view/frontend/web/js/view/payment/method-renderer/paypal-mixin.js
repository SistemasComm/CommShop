define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-billing-address'
], function ($, quote, selectBillingAddress) {
    'use strict';

    var mixin = {
        /**
         * Prepare data to place order
         * @param {Object} data
         */
        beforePlaceOrder: function (data) {
            let shippingAddress = quote.shippingAddress();
            let billingAddress = quote.billingAddress();
            let paypalData = {
                firstname: data.details['firstName'],
                lastname: data.details['lastName'],
                email: data.details['email']
            };

            this.setPaymentMethodNonce(data.nonce);

            if (shippingAddress === billingAddress) {
                selectBillingAddress(shippingAddress);
            } else {
                billingAddress = billingAddress || $.extend({}, shippingAddress, paypalData, true);
                selectBillingAddress(billingAddress);
            }

            this.customerEmail(data.details.email);
            this.placeOrder();
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
});
