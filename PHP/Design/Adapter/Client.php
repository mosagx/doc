<?php

include_once "EuroAdapter.php";
include_once "DollarCalc.php";

class Client
{
    private $requestNow;
    
    private $dollarRequest;

    public function __construct()
    {
        $this->requestNow = new EuroAdapter();
        $this->dollarRequest = new DollarCalc();
        $euro = "€";
        echo "Euros: $euro" . $this->makeAdapterRequest($this->requestNow) . "<br />";
        echo "Dollars: $" . $this->makeDollarRequest($this->dollarRequest) . "<br />";
    }

    private function makeAdapterRequest(ITarget $req)
    {
        return $req->requestCalc(40, 50);
    }

    private function makeDollarRequest(DollarCalc $req)
    {
        return $req->requestCalc(40, 50);   
    }
}

$worker = new Client();