# PHP_Inject

Dependency Injection Container for PHP 5.3+

From Wikipedia:

Dependency injection is a software design pattern that implements inversion of control and allows a program design to follow the dependency inversion principle.

http://en.wikipedia.org/wiki/Dependency_injection



## Goals

- Easy to use
- Inject configuration parameters as well as Objects
- High quality extendable code



## Workflow without Dependency Injection

Suppose we have following setup:

We have CreditCardProcessor class, in order to work, it needs kind of Database:

~~~php
class CreditCardProcessor{
	function __construct(){
		$this->db = new MySQLDatabase("localhost", "admin", "secret");
	}

	function process(){
	}
}
~~~

There are some problems with this approach:

- MySQLDatabase is **hard-wired** in the constructor. If we want to change it, for example connect to different database or for testing, we will need to edit the contructor.
- Code for instantiation e.g. new MySQLDatabase is mixed with business logic.

Not using Dependency Injection probably have single benefit:

- Code is clean and easy to understand, just by reading a single file.



## Dependency Injection - basic usage

Instead of **hard-wire** the MySQLDatabase class, we can pass interface Database as argument for the constructor.

~~~php
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

// CreditCardProcessor depends of Database interface
class CreditCardProcessor{
	function __construct(Database $db){
		$this->db = $db;
	}

	function process(){
	}
}
~~~

in such cases you probably need to do:

~~~php
$db  = new MySQLDatabase("localhost", "admin", "secret");
$ccp = new CreditCardProcessor($db);
$ccp->process();
~~~

...later if you want do to some tests, you will need to do:

~~~php
$db  = new MockDatabase();
$ccp = new CreditCardProcessor($db);
$ccp->process();
~~~

The benefits of Dependency Injection are:

- the class do not need to instanciate the objects using "new".
- the class behavour is controlled "outside" the class.
- great flexability, because the class can work with different implementations, without any changes.
- great abstraction, because the class does not really "knows" with what object it works.

There are some problems with this approach:

- lots of external code for instanciate the objects.
- instantiation code is mixed with business logic, e.g. difficult control over the code



## Dependency Injection with Factories

We can clean things a bit, if we use a Factory:

~~~php
interface CreditCardProcessorFactory{
	function getInstance();
}

class ProductionCreditCardProcessorFactory implements CreditCardProcessorFactory{
	function getInstance(){
		$db  = new MySQLDatabase("localhost", "admin", "secret");
		return new CreditCardProcessor($db);
	}
}

class TestCreditCardProcessorFactory implements CreditCardProcessorFactory{
	function getInstance(){
		$db  = new MockDatabase();
		return new CreditCardProcessor($db);
	}
}
~~~

then in case of production:

~~~php
$factory = new ProductionCreditCardProcessorFactory();
$ccp = $factory->getInstance();
$ccp->process();
~~~

... or in case of testing:

~~~php
$factory = new TestCreditCardProcessorFactory();
$ccp = $factory->getInstance();
$ccp->process();
~~~

The additional benefits of Dependency Injection + Factories are:

- instantiation code is not mixed with business logic

There are some problems with this approach:

- much more code and much more clases
- in all cases writting Factories is not fun :)



## Dependency Injection with PHP-Inject

This is why many languages offers Dependency Injections Containers.

Here is how this same example can be done using PHP-Inject:

~~~php
// production configuration
$conf = new injector\Configuration();
$conf->bind("db",	new injector\BindObject("MySQLDatabase"));
$conf->bind("host",	new injector\BindValue("localhost"));
$conf->bind("user",	new injector\BindValue("admin"));
$conf->bind("pass",	new injector\BindValue("pass"));

// test configuration
$conftest = new injector\Configuration();
$conftest->bind("db",	new injector\BindObject("MockDatabase"));
~~~

then in case of production:

~~~php
$injector = new injector\Injector(array($conf));
$ccp = $injector->provide("CreditCardProcessor");
$ccp->process();
~~~

... or in case of testing:

~~~php
$injector = new injector\Injector(array($conftest));
$ccp = $injector->provide("CreditCardProcessor");
$ccp->process();
~~~

The additional benefits of Dependency Injection with PHP-Inject are:

- instantiation code is not mixed with business logic
- injecting configuration parameters as well as Objects
- instanciate whole dependency tree, e.g. "everything"
- can instanciate "Singleton"-like Objects -
e.g. for example create only one connection to the database and provide it to all places where is necessary.
- less code, more control

There are some problems with this approach:

- because PHP not really have argument types, you need to use descriptive and different arguments.
For example if you connect to Redis and MySQL, you do not want to use $host for both arguments.
You will need to use different argument name, such $redis_host and $mysql_host, in order PHP_Inject to inject desirable values.



## Different types of Binds:

- **BindValue** - inject a value. The value can be any PHP type, but Objects are not good choice.
- **BindObject** - inject an Object. Object is instantiated using new and its dependencies are resolved.
- **BindFactory** - use a factory (PHP Callable) in order to get the value or instantiate the object. If $final is set to false, then return type is instantiated in a way similar to BindObject.
- **BindFileObject** - similar to BindObject, but is rather special case. Inject an Object that is defined in some file. File must contains only one Class definition.

## BindValue example

~~~php
// bind "localhost" to $host
$conf->bind("host",	new injector\BindValue("localhost"));
~~~

## BindObject example

~~~php
class MySQLDatabase{
}

// bind MySQLDatabase to $db
// if MySQLDatabase class have dependencies, they will be resolved.
$conf->bind("db",	new injector\BindObject("MySQLDatabase"));
~~~

## BindFactory example

~~~php
class MySQLDatabase{
}

// bind factory to $db
$conf->bind("db",	new injector\BindFactory(function(){
	return new MySQLDatabase();
});
~~~

... or using static method ...

~~~php
class MySQLDatabaseFactory{
	static function getInstance(){
		return new MySQLDatabase();
	}
}

// bind factory to $db
$conf->bind("db",	new injector\BindFactory("MySQLDatabaseFactory::getInstance");
~~~

... or using normal method ...

~~~php
class MySQLDatabaseFactory{
	function getInstance(){
		return new MySQLDatabase();
	}
}

// bind factory to $db
$factory = new MySQLDatabaseFactory();
$conf->bind("db",	new injector\BindFactory(array($factory, "getInstance"));
~~~

## BindFileObject example

in file "Foo.php"
~~~php
// classname do not need to be same as filename
class MyFoo{
}
~~~

in main code:
~~~php
// bind the class defined in file "Foo.php" (e.g. MyFoo) to $foo
// if MyFoo class have dependencies, they will be resolved.
$conf->bind("foo",	new injector\BindFileObject(__DIR__ . "/Foo.php"));
~~~

## [eof]
