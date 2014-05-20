# PHP_Inject

Dependency Injection Container for PHP 5.3+

From Wikipedia:

Dependency injection is a software design pattern that implements inversion of control and allows a program design to follow the dependency inversion principle.

(http://en.wikipedia.org/wiki/Dependency_injection)

## Goals

- Easy to use
- Inject configuration parameters as well as Objects
- High quality extendable code


## Basic usage of Dependency Injection

Suppose we have following setup:

```
interface Database{
}

class MySQLDatabase implements Database{
	function __construct($host, $user, $pass){
	}
}

class MockDatabase implements Database{
	function __construct($host, $user, $pass){
	}
}

class CreditCardProcessor{
	function __construct(Database $db){
	}
}
```

in such cases you probably need to do:

```
$db  = new MySQLDatabase("localhost", "admin", "secret");
$ccp = new CreditCardProcessor($db);
$ccp->usage();
```

...later if you want do to some tests, you will need to do:

```
$db  = new MockDatabase();
$ccp = new CreditCardProcessor($db);
$ccp->usage();
```

The benefits of Dependency Injection are:

- the class do not need to instanciate the objects using "new".
- the class behavour is controlled "outside" the class.
- the class works with interface or with abstract class.

There are some problems with this approach:

- lots of external code for instanciate the objects.
- instantiation code is mixed with business logic, e.g. difficult control over the code

## Basic usage with Dependency Injection + Factories

```
interface CreditCardProcessorFactory{
	function getInstance();
}

class MySQLCreditCardProcessorFactory implements CreditCardProcessorFactory{
	function getInstance(){
		$db  = new MySQLDatabase("localhost", "admin", "secret");
		return new CreditCardProcessor($db);
	}
}

class TestCreditCardProcessorFactory{
	function getInstance(){
		$db  = new MockDatabase();
		return new CreditCardProcessor($db);
	}
}
```

then in case of production:

```
$factory = new MySQLCreditCardProcessorFactory();
$ccp = $factory->getInstance();
$ccp->usage();
```

... or in case of testing:

```
$factory = new TestCreditCardProcessorFactory();
$ccp = $factory->getInstance();
$ccp->usage();
```

The additional benefits of Dependency Injection + Factories are:

- instantiation code is not mixed with business logic

There are some problems with this approach:

- much more code and much more clases
- in all cases writting Factories is not fun :)

## Basic usage with PHP-Inject

This is why many languages offers Dependency Injections Containers.

Here is how this same example can be done using PHP-Inject:

```
// production configuration
$conf = new injector\Configuration();
$conf->bind("db",	new injector\BindObject("MySQLDatabase"));
$conf->bind("host",	new injector\BindValue("localhost"));
$conf->bind("user",	new injector\BindValue("admin"));
$conf->bind("pass",	new injector\BindValue("pass"));

// test configuration
$conftest = new injector\Configuration();
$conftest->bind("db",	new injector\BindObject("MockDatabase"));
```

then in case of production:

```
$injector = new injector\Injector(array($conf));
$ccp = $injector->provide("CreditCardProcessor");
$ccp->usage();
```

... or in case of testing:

```
$injector = new injector\Injector(array($conftest));
$ccp = $injector->provide("CreditCardProcessor");
$ccp->usage();
```

## Different types of Binds:

- BindValue - inject a value. The value can be any PHP type, but Objects are not good choice.
- BindObject - inject an Object. Object is instantiated using new and its dependencies are resolved.
- BindFactory - use a factory (PHP Callable) in order to get the value or instantiate the object. If $final is set to false, then return type is instantiated in a way similar to BindObject.
- BindFileObject - similar to BindObject, but is rather special case. Inject an Object that is defined in some file. File must contains only one Class definition.

## BindValue example

```
// bind "localhost" to $host
$conf->bind("host",	new injector\BindValue("localhost"));
```

## BindObject example

```
class MySQLDatabase{
}

// bind MySQLDatabase to $db
// if MySQLDatabase class have dependencies, they will be resolved.
$conf->bind("db",	new injector\BindObject("MySQLDatabase"));
```

## BindFactory example

```
class MySQLDatabase{
}

// bind factory to $db
$conf->bind("db",	new injector\BindFactory(function(){
	return new MySQLDatabase();
});
```

... or using static method ...

```
class MySQLDatabase{
	static function getInstance(){
		return new MySQLDatabase();
	}
}

// bind factory to $db
$conf->bind("db",	new injector\BindFactory("MySQLDatabase::getInstance");
```

## BindFileObject example

in file "Foo.php"
```
// classname do not need to be same as filename
class MyFoo{
}
```

in main code:
```
// bind the class defined in file "Foo.php" (e.g. MyFoo) to $foo
// if MyFoo class have dependencies, they will be resolved.
$conf->bind("foo",	new injector\BindFileObject(__DIR__ . "/Foo.php"));
```

## [eof]
