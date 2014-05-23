<?
namespace injector;


interface AbstractInjector{
	/**
	 * get Reference to the $specifications array
	 *
	 * @return array reference
	 *
	 */
	function & specifications();


	/**
	 * Lazi constructor call,
	 *
	 * e.g. instanciate new object
	 *
	 * @param string $classname name of the class
	 * @return object
	 *
	 */
	function provide($classname);


	/**
	 * Lazi method call
	 *
	 * @param object|string $classname classname or instance of a class
	 * @param string $method method to be called
	 * @return mixed
	 *
	 */
	function callMethod($classname, $method);
}


