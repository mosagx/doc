<?php

include_once 'Product.php';

class GriphicProduct implements Product
{
    private $mfgProduct;

    public function getProperties()
    {
        $this->mfgProduct = "<!DOCTYPE html><html lang='en'>";
        $this->mfgProduct .= "<head>";
        $this->mfgProduct .= "<meta charset='UTF-8'>";
        $this->mfgProduct .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        $this->mfgProduct .= "<meta http-equiv='X-UA-Compatible' content='ie=edge'>";
        $this->mfgProduct .= "<title>Map Factory</title>";
        $this->mfgProduct .= "</head><body>";
        $this->mfgProduct .= "<img src='./Pic.jpg' width='500' height='500'>";
        $this->mfgProduct .= "</body></html>";

        return $this->mfgProduct;
    }
}
