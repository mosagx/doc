<?php

require 'Product.php';

class GriphicProduct implements Product
{
    private $mfgProduct;

    public function getProperties()
    {
        $this->mfgProduct = 'This is a graphic.';

        return $this->mfgProduct;
    }
}
