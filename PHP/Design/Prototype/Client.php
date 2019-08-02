<?php

// autoload
function f1($class_name)
{
    if (file_exists("./$class_name.php")) {
        require "./$class_name.php";
    }
}

spl_autoload_register('f1');

class Client
{
    private $market;
    private $manage;
    private $engineer;

    public function __construct()
    {
        $this->makeConProto();

        $Tess = clone $this->market;
        $this->setEmployee($Tess, "Tess Smith", 101, "ts101-1234", "ts-smith.png");
        $this->showEmployee($Tess);

        $Jacob = clone $this->market;
        $this->setEmployee($Jacob, "Jacob Jones", 102, "JJ101-4212", "jacob.png");
        $this->showEmployee($Jacob);

        $Ricky = clone $this->manage;
        $this->setEmployee($Ricky, "Ricky Rodriguez", 203, "RR203-1241", 'ricky.png');
        $this->showEmployee($Ricky);

        $John = clone $this->engineer;
        $this->setEmployee($John, "John Jackson", 301, "jj302-1454", 'john.png');
        $this->showEmployee($John);

        $Olivia = clone $this->engineer;
        $this->setEmployee($Olivia, "Olivia Perez", 302, "op301-1278", "olivia.png");
        $this->showEmployee($Olivia);
    }

    private function makeConProto()
    {
        $this->market = new Marketing();
        $this->manage = new Management();
        $this->engineer = new Engineering();
    }

    private function showEmployee(IAcmePrototype $employeeNow)
    {
        $px = $employeeNow->getPic();
        echo "img: $px <br />";
        echo "Name: " . $employeeNow->getName() . "<br />";
        echo "Department: " . $employeeNow->getDept() . " : " . $employeeNow::UNIT . "<br />";
        echo "ID: " . $employeeNow->getId() . " <p/>";
    }

    private function setEmployee(IAcmePrototype $employeeNow, $nm, $dp, $id, $px)
    {
        $employeeNow->setName($nm);
        $employeeNow->setDept($dp);
        $employeeNow->setId($id);
        $employeeNow->setPic($px);
    }
}

$worker = new Client();