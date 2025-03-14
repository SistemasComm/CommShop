/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
 define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/place-order'
], function ($, quote, urlBuilder, customer, placeOrderService) {
    'use strict';

    return function (paymentData, messageContainer) {
        var serviceUrl, payload;


        if ($("#co-payment-form ._active input[name='billing-address-same-as-shipping']:first").is(':checked')) {
            quote.billingAddress(quote.shippingAddress());
        }

        payload = {
            cartId: quote.getQuoteId(),
            billingAddress: quote.billingAddress(),
            paymentMethod: paymentData
        };

        if (customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
        } else {
            serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                quoteId: quote.getQuoteId()
            });
            payload.email = quote.guestEmail;
        }
        var password = $('[name="code-password"] [name="code-password"]').val();
        var confirm_password = $('[name="code-confirm_password"] [name="code-confirm_password"]').val();
        var mm_dob = $('[name="shippingAddress.mm_dob"] [name="mm_dob"]').val();
        var mm_gender = $('[name="shippingAddress.mm_gender"] [name="mm_gender"]').val();
        var inscricao_estadual = $('[name="shippingAddress.inscricao_estadual"] [name="inscricao_estadual"]').val();
        var razao_social = $('[name="shippingAddress.razao_social"] [name="razao_social"]').val();
        var comments = $('[name="comment-code"]:first').val();
        payload.confirm_password = confirm_password;
        payload.code_password = password;
        payload.mm_dob = mm_dob;
        payload.mm_gender = mm_gender;
        payload.inscricao_estadual = inscricao_estadual;
        payload.razao_social = razao_social;
        payload.comments = comments;
        return placeOrderService(serviceUrl, payload, messageContainer);
    };
});
