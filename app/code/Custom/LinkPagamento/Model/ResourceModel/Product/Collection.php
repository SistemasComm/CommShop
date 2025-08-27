<?php
namespace Custom\LinkPagamento\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Custom\LinkPagamento\Model\Product as Model;
use Custom\LinkPagamento\Model\ResourceModel\Product as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
