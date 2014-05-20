# PHP_Inject

Dependency Injection Container for PHP 5.3+

From Wikipedia:

Dependency injection is a software design pattern that implements inversion of control and allows a program design to follow the dependency inversion principle.

(http://en.wikipedia.org/wiki/Dependency_injection)

## Goals

- Easy to use
- Inject configuration parameters as well as Objects
- High quality extendable code


## Basic usage

Suppose we have following setup:

```
class MySQLDatabase{
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

There are several problems with this approach.
You probably will need to create Factory classes for managing the dependencies:

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

... or in case of production:

```
$factory = new TestCreditCardProcessorFactory();
$ccp = $factory->getInstance();
$ccp->usage();
```

In all cases writting Factories is not fun.

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

// get CreditCardProcessor for production:
$injector = new injector\Injector(array($conf));
ccp = $injector->provide("CreditCardProcessor");

// get CreditCardProcessor for testing:
$injector = new injector\Injector(array($conftest));
ccp = $injector->provide("CreditCardProcessor");
```


## [eof]
