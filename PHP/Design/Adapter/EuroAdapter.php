<?php

include_once "ITarget.php";
include_once "EuroCalc.php";

class EuroAdapter extends EuroCalc implements ITarget
{
    public function __construct()
    {
        $this->requester();
    }
    public function requester()
    {
        $this->rate = .8;
        return $this->rate;
    }
}