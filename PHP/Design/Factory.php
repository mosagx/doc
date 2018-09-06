<?php
class Factory
{
    public function Factory ()
    {
        // TODO
    }

    public function create($obj)
    {
        switch ($obj) {
            case 'A':
                return new A();
                break;
            case 'B':
                return new B();
                break;
            default:
                return new C();
                break;
        }
    }
}
$obj = new Factory();
$obj->create('A');