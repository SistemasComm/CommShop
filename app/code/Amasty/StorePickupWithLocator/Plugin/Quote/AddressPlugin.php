<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Plugin\Quote;

use Amasty\StorePickupWithLocator\Model\Quote\QuoteAddressResolver;

/**
 * Plugin for fill empty data
 */
class AddressPlugin
{
    /**
     * @var AddressHelper
     */
    private $quoteAddressResolver;

    public function __construct(
        QuoteAddressResolver $quoteAddressResolver
    ) {
        $this->quoteAddressResolver = $quoteAddressResolver;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterAddData(
        \Magento\Quote\Model\Quote\Address $subject,
        $result
    ) {
        $this->quoteAddressResolver->fillEmpty($subject);

        return $result;
    }
}
