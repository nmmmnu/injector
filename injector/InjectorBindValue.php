<?
namespace injector;

class InjectorBindValue implements InjectorBind{
	private $_value;


	function __construct($value){
		$this->_value = $value;
	}


	function provide(){
		return $this->_value;
	}


	function isSingleton(){
		return false;
	}


	function isFinal(){
		return true;
	}
}

