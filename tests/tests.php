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
	public $name = 5;

	function __construct($host, $port){
		printf("%s::__construct('%s', %d);\n", __CLASS__, $host, $port);
	}

	function someMethod($port, $host){
		printf("%s::someMethod('%s', %d);\n", __CLASS__, $host, $port);
		return 123;
	}
}


$classname = __NAMESPACE__ . "\\" . "bla";
$method = "someMethod";


// =============================================


$conf = new \injector\Configuration();
$conf->bind("host", new \injector\BindValue("localhost"));
$conf->bind("port", new \injector\BindValue(80));


// =============================================


$injector = new \injector\Injector( /* array($conf) */ );

$injector->specifications()["fake"] = "bla?!?";	// put fake data first
$injector->specifications()["fake"] = null;	// put null
$injector->specifications()["conf"] = $conf;	// put real thing


// =============================================


$bla = $injector->provide($classname);

assert($bla->name == 5);

assert($injector->callMethod($bla, $method) == 123);

assert($injector->provideAndCallMethod($classname, $method) == 123);
assert($injector->provideAndCallMethod($classname, $method) == 123);


