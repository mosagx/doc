<?php

// 公共接口
interface Visit 
{
    public function go();
}

// 实现类
class Leg implements Visit 
{
    public function go()
    {
        echo 'walk to Beijing';
    }
}

class Car implements Visit
{
    public function go()
    {
        echo 'drive car to Beijing';
    }
}

class Train implements Visit
{
    public function go()
    {
        echo 'go to Beijing by train';
    }
}

// 应用实例类
class Traveller
{
    protected $tool;
    public function __construct()
    {
        // 产生依赖
        $this->tool = new Leg();
    }

    public function visitBeijing()
    {
        // 实现方法
        $this->tool->go();
    }
}

// 应用
$traver = new Traveller();
$traver->visitBeijing();

