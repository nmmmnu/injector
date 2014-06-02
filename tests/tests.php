<?
namespace tests;

require_once __DIR__ . "/../__autoload.php";


// =============================================


function assert_setup(){
        assert_options(ASSERT_ACTIVE,   true);
        assert_options(ASSERT_BAIL,     true);
        assert_options(ASSERT_WARNING,  false);
        assert_options(ASSERT_CALLBACK, function($script, $line, $message){
		echo "Condition failed in $script, Line: $line\n";

		if ($message)
			echo "Message: $message\n";

		echo "\n";
	});
}

assert_setup();


// =============================================


function TestInjectorBind(\injector\Bind $spec, $expect, $singleton){
	printf("Testing %s...\n", get_class($spec));

	assert($spec->provide()     == $expect);
	assert($spec->isSingleton() == $singleton);
}

TestInjectorBind(new \injector\BindValue(5), 5, false);
TestInjectorBind(new \injector\BindObject("bla"), "bla", true);
TestInjectorBind(new \injector\BindFileObject(__DIR__ . "/data_testclass.php"), "bla\\bla\\testclass", true);
TestInjectorBind(new \injector\BindFactory(function(){ return 5; }), 5, true);


// =============================================


class bla{
	const RESULT = 123;

	private $_id;

	function __construct($host, $port){
		// add rand(), because test fail on Windows
		$this->_id = uniqid() . rand();

		printf("%s::__construct('%s', %d);\n", __CLASS__, $host, $port);
	}

	function process($port, $host){
		printf("%s::someMethod('%s', %d);\n", __CLASS__, $host, $port);
		return self::RESULT;
	}

	function getID(){
		return $this->_id;
	}
}


$classname = __NAMESPACE__ . "\\" . "bla";


// =============================================


$conf = new \injector\Configuration();
$conf->bind("host", new \injector\BindValue("localhost"));
$conf->bind("port", new \injector\BindValue(80));


$injector = new \injector\Injector( /* array($conf) */ );

$injector->specifications()["fake1"] = "bla?!?";	// put fake data first
$injector->specifications()["fake2"] = null;		// put null
$injector->specifications()["conf"] = $conf;		// put real thing


$sInjector = new \injector\SingletonInjector($injector);


// =============================================


echo "\n";
echo "Testing Injector::provide\n";

$bla1 = $injector->provide($classname);
$bla2 = $injector->provide($classname);

// must provide two different objects
assert($bla1 !== $bla2);

// check what it provided
assert(get_class($bla1) === $classname);


echo "\n";
echo "Testing Injector::call with object\n";
assert($injector->callMethod($bla1, "process") == bla::RESULT);


echo "\n";
echo "Testing Injector::call with string\n";
assert($injector->callMethod($classname, "process") == bla::RESULT);

// must provide two different objects
assert($injector->callMethod($classname, "getID") !== $injector->callMethod($classname, "getID"));


// =============================================


echo "\n";
echo "Testing SingletonInjector::call with string\n";
$bla1 = $sInjector->provide($classname);
$bla2 = $sInjector->provide($classname);

// must provide same object
assert($bla1 === $bla2);

// must provide same object
assert($sInjector->callMethod($classname, "getID") == $sInjector->callMethod($classname, "getID"));


// =============================================


echo "All tests passed!!!\n";
echo "You are awesome :)\n";

exit(0);

