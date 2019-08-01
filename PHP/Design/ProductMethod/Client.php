<?php

require 'TextFactory.php';
require 'GriphicFactory.php';

class Client
{
    private $someTextProduct;
    private $someGriphicProduct;

    public function __construct()
    {
        $this->someTextProduct = new TextFactory();
        echo $this->someTextProduct->startFactory().'<br />';
        $this->someGriphicProduct = new GriphicFactory();
        echo $this->someGriphicProduct->startFactory().'<br />';
    }
}

$worker = new Client();
