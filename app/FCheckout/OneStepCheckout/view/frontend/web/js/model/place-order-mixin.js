/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'FCheckout_OneStepCheckout/js/model/comment-assigner',
    'FCheckout_OneStepCheckout/js/model/password-assigner'
], function ($, wrapper, commentAssigner, passwordAssigner) {
    'use strict';

    return function (placeOrderService) {

        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderService, function (originalAction, serviceUrl, payload, messageContainer) {
            commentAssigner(payload);
            passwordAssigner(payload);

            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});
