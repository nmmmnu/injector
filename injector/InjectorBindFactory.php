<?
namespace injector;

class InjectorBindFactory implements InjectorBind{
	private $_callable;
	private $_singleton;


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

