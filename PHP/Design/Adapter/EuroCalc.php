<?php

class EuroCalc
{
    private $euro;

    private $product;

    private $service;

    public $rate = 1;

    public function requestCalc($productNow, $serviceNow)
    {
        $this->product = $productNow;
        $this->service = $serviceNow;
        $this->euro = $this->product + $this->service;
        // $backtrace = debug_backtrace();
        // echo json_encode($backtrace);

        return $this->requestTotal();
    }

    public function requestTotal()
    {
        $this->euro *= $this->rate;

        return $this->euro;
    }
}
