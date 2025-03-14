<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Locator for Magento 2
 */

namespace Amasty\Storelocator\Model\DataCollector\Location;

use Amasty\Storelocator\Api\Data\LocationInterface;
use Amasty\Storelocator\Block\View\Reviews;
use Magento\Framework\View\LayoutInterface;

class RatingHtmlCollector implements LocationCollectorInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function initialize(): void
    {
    }

    public function collect(LocationInterface $location): void
    {
        $result = $this->layout->createBlock(Reviews::class)
            ->setData('location', $location)
            ->setTemplate('Amasty_Storelocator::rating.phtml')
            ->toHtml();
        $location->setRating($result);
    }
}
