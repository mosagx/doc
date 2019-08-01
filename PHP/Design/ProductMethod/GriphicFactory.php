<?php

require 'Creator.php';
require 'GriphicProduct.php';

class GriphicFactory extends Creator
{
    protected function factoryMethod()
    {
        $product = new GriphicProduct();

        return $product->getProperties();
    }
}
