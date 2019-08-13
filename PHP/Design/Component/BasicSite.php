<?php
 
include_once "IComponent.php";

class BasicSite extends IComponent
{
    public function __construct()
    {
        $this->site = "Base Site.";
    }

    public function getSite()
    {
        return $this->site;
    }

    public function getPrice()
    {
        return 1200;
    }
}