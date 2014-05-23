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

class bla{
	public $name = 5;
}

class foo{
	function __construct(bla $b){
		var_dump($b);
		var_dump($b->name);
	}
}


// =============================================

$injector = new \injector\Injector(array());
$bla = $injector->provide(__NAMESPACE__ . "\\" . "foo");

