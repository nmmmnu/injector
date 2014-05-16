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
}

// =============================================

function TestInjectorSpec(InjectorSpec $spec, $expect, $singleton){
	printf("Testing %s...\n", get_class($spec));

	assert($spec->provide()     == $expect);
	assert($spec->isSingleton() == $singleton);
}

TestInjectorSpec(new InjectorSpecValue(5), 5, false);
TestInjectorSpec(new InjectorSpecObject("bla"), "bla", true);
TestInjectorSpec(new InjectorSpecFactory(function(){ return 5; }), 5, true);

// =============================================

$specs = new InjectorSpecs();
$specs->bind("host", new InjectorSpecValue("localhost"));
$specs->bind("port", new InjectorSpecValue(80));

$injector = new Injector(array($specs));
$bla = $injector->provide(__NAMESPACE__ . "\\" . "bla");




