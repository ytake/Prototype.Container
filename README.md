# Prototype.Container(develop)
[![Build Status](https://travis-ci.org/ytake/Prototype.Container.svg?branch=develop)](https://travis-ci.org/ytake/Prototype.Container)

easy dependency injection / service container  
**for >=php5.5**

refactor [Iono.Container](https://github.com/ytake/Iono.Container)
## future plan
for Annotations
 - @Autowired
 - @Component
 - @Inject
 - @Qualifier
 - @Resource
 - @Scope
 - @Value  

and lightweight Compiler..

## usage

### container instance
```php
$this->container = new \Iono\Container\Container;
```

for concrete & abstract class
```php
/**
 * Interface ResolveInterface
 */
interface ResolveInterface
{
    public function getInstance();
}

/**
 * Class ResolveClass
 */
class ResolveClass implements ResolveInterface
{
    protected $value;
    public function getInstance()
    {
        return $this;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
    public function getValue()
    {
        return $this->value;
    }
}
```

### concrete & abstract bindings(prototype)

```php
$this->container->binder("ResolveInterface", "ResolveClass");
$instance = $this->container->newInstance("ResolveInterface");
```

### singleton
```php
$this->container->singleton("ResolveInterface", "ResolveClass");
$instance = $this->container->newInstance("ResolveInterface");
```

### service locator
string?
```php
$context = $this->container->newInstance("iono.container.tests");
```
object?
```php
$this->container->binder("std.class", new \stdClass());
$context = $this->container->newInstance("std.class");
```

### closure

```php
$this->container->binder("closure", function () {
    return new \stdClass();
});
$this->container->newInstance('closure');
```

### bindings & parameters

```php
$this->container->setParameters("ResolveConstructor", ["arg" => "dependency2"]);
$this->container->binder("ResolveInterface", "ResolveClass");
$class = $this->container->newInstance("ResolveConstructor");
```
parameters override
```php
$class = $this->container->newInstance("ResolveConstructor", ["arg" => "dependency3"]);
//
$class = $this->container->newInstance("ResolveConstructor", ["arg" => $this->container->newInstance("stdclass")]);
```
