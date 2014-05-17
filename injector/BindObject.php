<?
namespace injector;


/**
 * InjectorBind to Object
 *
 * the object is later re-evaluated
 *
 */
class BindObject implements Bind{
	private $_classname;
	private $_singleton;


	/**
	 * constructor
	 *
	 * @param string $classname name of the class
	 * @param boolean $singleton
	 *
	 */
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

