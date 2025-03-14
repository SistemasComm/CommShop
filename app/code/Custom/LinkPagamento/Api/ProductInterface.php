<?php
namespace Custom\LinkPagamento\Api;

interface ProductInterface
{
    /**
     * Post product data
     *
     * @param string $sku
     * @param int $quantity
     * @param float $price
     * @return string
     */
    public function postProduct($sku, $quantity, $price);
}
