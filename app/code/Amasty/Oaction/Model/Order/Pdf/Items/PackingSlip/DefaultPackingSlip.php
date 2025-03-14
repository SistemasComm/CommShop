<?php
declare(strict_types=1);

namespace Amasty\Oaction\Model\Order\Pdf\Items\PackingSlip;

use Magento\Sales\Model\Order\Pdf\Items\Shipment\DefaultShipment;

class DefaultPackingSlip extends DefaultShipment
{
    /**
     * Draw item line and correct Qty value
     *
     * @return void
     */
    public function draw(): void
    {
        $item = $this->getItem();
        $item->setQty($item->getQtyOrdered());
        $this->setItem($item);

        parent::draw();
    }
}
