<?php
// 设计容器，装载实例或提供实例回调函数
class Container
{
    protected $bindings = [];

    // 绑定接口和生成相应实例的回调函数
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if ( ! $concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    // 默认生成实例的回调函数
    public function getClosure($abstract, $concrete)
    {
        // 生成实例的回调函数:build函数中的 $concrete($this)
        return function($c) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $c->method($concrete);
        };
    }

    // 生成实例对象
    public function make($abstract)
    {
        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }
        return $object;
    }

    // 验证是否绑定
    public function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    // 获取绑定的回调函数
    public function getConcrete($abstract)
    {
        if ( ! isset($this->bindings[$abstract])) {
            return $abstract;
        }
        return $this->bindings[$abstract]['concrete'];
    }

    // 实例化对象
    public function build($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        $reflector = new ReflectionClass($concrete);
        if ( ! $reflector->isInstantiable()) {
            echo $message = "Target [$concrete] is not instantiable.";
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }
        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    // 解决通过反射机制实例化对象时的依赖
    protected function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                $dependencies[] = NULL;
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (array) $dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter) 
    {
        return $this->make($parameter->getClass()->name);
    }
}
// Example
$app = new Container();
// 容器填充
$app->bind("Visit", "Train");
$app->bind("traveller", "Traveller");
// 通过容器实现依赖注入，完成类的实例化
$traver = $app->make("traveller");
$traver->visitBeijing();