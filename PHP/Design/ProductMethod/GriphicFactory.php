<?php

include_once 'Creator.php';
include_once 'GriphicProduct.php';

class GriphicFactory extends Creator
{
    protected function factoryMethod()
    {
        $product = new GriphicProduct();

        return $product->getProperties();
    }
}
