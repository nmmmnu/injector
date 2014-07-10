<?
namespace injector;


/**
 * InjectorBind to Factory, e.g. PHP callable.
 *
 */
 class BindFactory implements Bind{
	private $_callable;
	private $_singleton;


	/**
	 * constructor
	 *
	 * @param callable $callable
	 * @param boolean $singleton
	 * @param boolean $final controls $this->isFinal(), this gives you possibility to return a type
	 *
	 */
	function __construct($callable, $singleton = true, $final = true){
		$this->_callable  = $callable;
		$this->_singleton = $singleton;
		$this->_final     = $final;
	}


	function provide(){
		$callback = $this->_callable;
		return $callback();
	}


	function isSingleton(){
		return $this->_singleton;
	}


	function isFinal(){
		return $this->_final;
	}


	static function test(){
		return tests\TestInjector::testBind(new self(function(){ return 5; }), 5, true);
	}
}

