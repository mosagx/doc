<?php

class DollarCalc
{
    // 价格
    private $dollar;

    // 产品
    private $product;

    // 佣金
    private $service;

    // 汇率
    public $rate = 1;

    public function requestCalc($productNow, $serviceNow)
    {
        $this->product = $productNow;
        $this->service = $serviceNow;
        $this->dollar = $this->service + $this->product;

        return $this->requestTotal();
    }

    public function requestTotal()
    {
        $this->dollar *= $this->rate;

        return $this->dollar;
    }
}
