<?
namespace injector;

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

// =============================================

function TestInjectorBind(InjectorBind $spec, $expect, $singleton){
	printf("Testing %s...\n", get_class($spec));

	assert($spec->provide()     == $expect);
	assert($spec->isSingleton() == $singleton);
}

TestInjectorBind(new InjectorBindValue(5), 5, false);
TestInjectorBind(new InjectorBindObject("bla"), "bla", true);
TestInjectorBind(new InjectorBindFileObject(__DIR__ . "/data_testclass.php"), "bla\\bla\\testclass", true);
TestInjectorBind(new InjectorBindFactory(function(){ return 5; }), 5, true);

// =============================================

$specs = new InjectorConfiguration();
$specs->bind("host", new InjectorBindValue("localhost"));
$specs->bind("port", new InjectorBindValue(80));

$injector = new Injector(array($specs));
$bla = $injector->provide(__NAMESPACE__ . "\\" . "bla");

assert($bla->name == 5);

assert($injector->callMethod($bla, "someMethod") == 123);
