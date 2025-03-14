<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Model\ResourceModel;

use Amasty\StorePickupWithLocator\Api\Data\QuoteInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Quote extends AbstractDb
{
    /**
     * Table Date Time
     */
    public const TABLE = 'amasty_storepickup_quote';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(self::TABLE, QuoteInterface::ID);
    }
}
