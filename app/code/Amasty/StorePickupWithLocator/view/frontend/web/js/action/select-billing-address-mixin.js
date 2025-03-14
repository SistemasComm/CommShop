define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function (wrapper, quote) {
    'use strict';

    /**
     * Restrict select store location address as billing address
     */
    return function (selectBillingAddressAction) {
        return wrapper.wrap(selectBillingAddressAction, function (original, address) {
            if (address?.getType() !== 'store-pickup-address'
                || quote.shippingMethod().carrier_code === 'amstorepickup') {
                original(address);
            }
        });
    };
});
