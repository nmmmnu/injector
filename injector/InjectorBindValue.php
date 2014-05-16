<?
namespace injector;


/**
 * InjectorBind to Value
 *
 * this is used mostly for configuration purposes
 *
 */
class InjectorBindValue implements InjectorBind{
	private $_value;


	/**
	 * constructor
	 *
	 * @param mixed $value value to be returned from $this->provide()
	 *
	 */
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

