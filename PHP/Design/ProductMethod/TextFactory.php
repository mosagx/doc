<?php

require 'Creator.php';
require 'TextProduct.php';

class TextFactory extends Creator
{
    protected function factoryMethod()
    {
        $product = new TextProduct();

        return $product->getProperties();
    }
}
