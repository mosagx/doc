<?php

include_once "IComponent.php";

abstract class Decorator extends IComponent
{
    // 继承 getSite()和getPrice()
    // 这仍是一个抽象类
    // 这里不实现任何一个抽象方法
    // 任务是维护Component引用
}