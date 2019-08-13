<?php

abstract class IComponent
{
    protected $date;
    protected $ageGroup;
    protected $feature;

    abstract public function setAge($ageNow);

    abstract public function getAge();

    abstract public function getFeature();

    abstract public function setFeature($fea);
}
