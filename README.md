# Prototype.Container(develop)
[![Build Status](http://img.shields.io/travis/ytake/Prototype.Container/develop.svg?style=flat-square)](https://travis-ci.org/ytake/Prototype.Container)
[![Coverage Status](http://img.shields.io/coveralls/ytake/Prototype.Container/develop.svg?style=flat-square)](https://coveralls.io/r/ytake/Prototype.Container?branch=develop)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/Prototype.Container.svg?style=flat-square)](https://scrutinizer-ci.com/g/ytake/Prototype.Container/?branch=develop)
[![Dependency Status](https://www.versioneye.com/user/projects/54d0e9e83ca0840b1900003c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54d0e9e83ca0840b1900003c)
![Iono.Container](http://img.shields.io/badge/iono-container-black.svg?style=flat-square)  

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d8a4459f-1af6-4bd6-b5f0-6b70693d6a44/big.png)](https://insight.sensiolabs.com/projects/d8a4459f-1af6-4bd6-b5f0-6b70693d6a44)

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
