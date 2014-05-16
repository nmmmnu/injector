<?
namespace injector;

class InjectorSpecObject implements InjectorSpec{
	private $_classname;
	private $_singleton;


	function __construct($classname, $singleton = true){
		$this->_classname = $classname;
		$this->_singleton = $singleton;
	}


	function provide(){
		return $this->_classname;
	}


	function isSingleton(){
		return $this->_singleton;
	}


	function isFinal(){
		return false;
	}
}

