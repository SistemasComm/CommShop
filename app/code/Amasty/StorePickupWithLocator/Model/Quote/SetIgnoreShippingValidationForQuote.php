<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Model\Quote;

use Amasty\StorePickupWithLocator\Model\Carrier\Shipping;

class SetIgnoreShippingValidationForQuote
{
    /**
     * Disable Shipping Validation
     *
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function execute($quote)
    {
        if ($quote->getShippingAddress()->getShippingMethod() === Shipping::SHIPPING_NAME) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShouldIgnoreValidation(true);
        }
    }
}
