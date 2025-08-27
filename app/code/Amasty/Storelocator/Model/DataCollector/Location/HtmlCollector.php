<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Locator for Magento 2
 */

namespace Amasty\Storelocator\Model\DataCollector\Location;

use Amasty\Storelocator\Api\Data\LocationInterface;
use Amasty\Storelocator\Model\ConfigHtmlConverter;

class HtmlCollector implements LocationCollectorInterface
{
    /**
     * @var ConfigHtmlConverter
     */
    private $configHtmlConverter;

    public function __construct(
        ConfigHtmlConverter $configHtmlConverter
    ) {
        $this->configHtmlConverter = $configHtmlConverter;
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function initialize(): void
    {
    }

    public function collect(LocationInterface $location): void
    {
        $this->configHtmlConverter->setHtml($location);
    }
}
