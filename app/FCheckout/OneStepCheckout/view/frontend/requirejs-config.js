var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'FCheckout_OneStepCheckout/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/model/quote': {
                'FCheckout_OneStepCheckout/js/model/quote-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/action/select-payment-method':
                'FCheckout_OneStepCheckout/js/action/select-payment-method'
        },
        "*": {
            "Magento_Checkout/js/view/billing-address": "FCheckout_OneStepCheckout/js/view/billing-address"
        },
        '*': {
            'Magento_Checkout/js/action/place-order':'FCheckout_OneStepCheckout/js/action/place-order'
        }
    }
};