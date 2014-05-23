<?
namespace injector;


/**
 * Handle Dependency Injections in Singleton-like clases.
 *
 */
class Singleton{
	private $_singletons = array();
	private $_injector;


	/**
	 * constructor
	 *
	 * @param Injector $injector injector to be used
	 *
	 */
	function __construct(Injector $injector){
		$this->_injector = $injector;
	}


	/**
	 * Lazi constructor call,
	 *
	 * e.g. instanciate new object
	 *
	 * @param string $classname name of the class
	 * @return object
	 *
	 */
	function provide($classname){
		if (! isset($this->_singletons[$classname]))
			$this->_singletons[$classname] = $this->_injector->provide($classname);

		return $this->_singletons[$classname];
	}


	/**
	 * Create Singleton-like instance, then Lazi method call
	 *
	 * @param string $classname name of the class
	 * @param string $method method to be called
	 * @return mixed
	 *
	 */
	function callMethod($classname, $method){
		$instance = $this->provide($classname);

		return $this->_injector->callMethod($instance, $method);
	}
}


