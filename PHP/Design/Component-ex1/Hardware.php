<?php

include_once 'Decorator.php';
include_once 'IComponent.php';

class Hardware extends Decorator
{
    private $hardwareNow;

    private $box = [
        "mac"  => "Macintosh",
        "dell" => "DELL",
        "hp"   => "Hewlett-Packard",
        "lin"  => "Linux",
    ];

    public function __construct(IComponent $dateNow)
    {
        $this->date = $dateNow;
    }

    public function setFeature($hdw)
    {
        $this->hardwareNow = $this->box[$hdw];
    }

    public function getFeature()
    {
        $output = $this->date->getFeature();
        $fmt = "<br/>&nbsp;&nbsp;";
        $output .= "$fmt Current Hardware: ";
        $output .= $this->hardwareNow;

        return $output;
    }
}