<?
namespace injector;


/**
 * InjectorBind to Factory, e.g. PHP callable.
 *
 */
 class InjectorBindFactory implements InjectorBind{
	private $_callable;
	private $_singleton;


	/**
	 * constructor
	 *
	 * @param callable $callable
	 * @param boolean $singleton
	 *
	 */
	function __construct($callable, $singleton = true){
		$this->_callable  = $callable;
		$this->_singleton = $singleton;
	}


	function provide(){
		$callback = $this->_callable;
		return $callback();
	}


	function isSingleton(){
		return $this->_singleton;
	}


	function isFinal(){
		return true;
	}
}

