<?
namespace injector;


/**
 * Handle Dependency Injections in Singleton-like clases.
 *
 */
class SingletonInjector implements AbstractInjector{
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


	function & specifications(){
		return $this->_injector->specifications();
	}


	function provide($classname){
		if (! isset($this->_singletons[$classname]))
			$this->_singletons[$classname] = $this->_injector->provide($classname);

		return $this->_singletons[$classname];
	}


	function callMethod($classname, $method){
		$instance = $this->provide($classname);

		return $this->_injector->callMethod($instance, $method);
	}
}


