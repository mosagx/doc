<?php

include_once 'Decorator.php';
include_once 'IComponent.php';

class ProgramLang extends Decorator
{
    private $languageNow;

    private $language = [
        'php' => 'PHP',
        'cs'  => 'C#',
        'js'  => 'JavaScript',
        'as3' => 'ActionScript 3.0',
    ];

    public function __construct(IComponent $dateNow)
    {
        $this->date = $dateNow;
    }

    public function setFeature($lan)
    {
        $this->languageNow = $this->language[$lan];
    }

    public function getFeature()
    {
        $output = $this->date->getFeature();
        $fmat = '<br/>&nbsp;&nbsp;';
        $output .= "$fmat Preferred programming languate: ";
        $output .= $this->languageNow;

        return $output;
    }
}
