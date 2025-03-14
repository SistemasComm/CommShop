<?php
namespace MestreMage\ShippingQuoteProductPage\Api;

interface ShippingQuoteProductPageInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $value Users value.
     * @return string Greeting message with users value.
     */
    public function shippingquoteproductpage();
}