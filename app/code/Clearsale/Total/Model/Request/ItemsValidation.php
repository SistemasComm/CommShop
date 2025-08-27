<?php

namespace Clearsale\Total\Model\Request;

class ItemsValidation extends Validation
{
    protected $_itemsArray = [];

    /**
     * @return bool
     */
    public function isValidated()
    {
        if (!$this->validateProductIdentification()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function validateProductIdentification()
    {
        $this->clearItems();

        foreach ($this->_inputParams['items'] as $item) {
            if (!$item->getName()) {
                $this->addError('Item ' . $item->getSku() . ' not identified for order ' . $this->_inputParams['order']->getIncrementId());
                return false;
            }

            $item = [
                'code' => $item->getSku(),
                'name' => $item->getName(),
                'value' => (float)$item->getPrice(),
                'amount' => (int)$item->getQtyOrdered()
            ];
            array_push($this->_itemsArray, $item);
        }

        return true;
    }

    /**
     *
     */
    protected function clearItems()
    {
        $this->_itemsArray = [];
    }

    /**
     * @return array
     */
    public function getItemsArray()
    {
        return $this->_itemsArray;
    }
}
