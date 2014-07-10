<?
namespace injector\tests;


class TestClass{
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
