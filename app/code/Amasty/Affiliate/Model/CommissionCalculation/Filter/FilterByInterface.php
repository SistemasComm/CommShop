<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\CommissionCalculation\Filter;

use Amasty\Affiliate\Api\Data\ProgramCommissionCalculationInterface;

interface FilterByInterface
{
    /**
     * Filter order items depending on commission calculation configuration
     *
     * @param ProgramCommissionCalculationInterface $commissionCalculation
     * @param array $orderItems
     */
    public function execute(ProgramCommissionCalculationInterface $commissionCalculation, array &$orderItems): void;
}
