<?php
/**
 * 依赖注入
 */
class A
{
    public $b;
    public $c;
    public function A($b, $c) 
    {
        $this->b = $b;
        $this->c = $c;
    }

    public function Method()
    {
        $this->b->Method();
        $this->c->Method();
    }
}

$a = new A(new B(), new C());
// 产生依赖 A依赖B和C 当B或C变动时 无需修改A  A类解耦
$a->Method();