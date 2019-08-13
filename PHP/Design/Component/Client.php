<?php

function f1($class_name)
{
    if (file_exists("./$class_name.php")) {
        include "./$class_name.php";
    }
}

spl_autoload_register('f1');

class Client
{
    private $basicSite;

    public function __construct()
    {
        $this->basicSite = new BasicSite();
        $this->basicSite = $this->wrapComponent($this->basicSite);

        $siteShow = $this->basicSite->getSite();
        $format = "<br/>&nbsp;&nbsp;<strong>Total: $";
        $price = $this->basicSite->getPrice();

        echo $siteShow . $format . $price . "</strong><p/>";
    }

    private function wrapComponent(IComponent $component)
    {   
        $component = new Maintenance($component);
        $component = new Video($component);
        $component = new Database($component);
        return $component;
    }
}

$worker = new Client();