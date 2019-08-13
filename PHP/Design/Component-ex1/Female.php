<?php

require_once "IComponent.php";

class Female extends IComponent
{
    public function __construct()
    {
        $this->date = "Female";
        $this->setFeature("<br/>Girl programmer features: ");
    }

    public function getAge()
    {
        return $this->ageGroup;
    }

    public function setAge($ageNow)
    {
        $this->ageGroup = $ageNow;
    }

    public function getFeature()
    {
        return $this->feature;
    }

    public function setFeature($fea)
    {
        $this->feature = $fea;
    }
}