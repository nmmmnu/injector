<?
namespace injector;

class InjectorSpecs{
	private $_store = array();

	function bind($from, InjectorSpec $provider){
		$this->_store[$from] = $provider;
	}

	function get($from){
		if (! isset($this->_store[$from]))
			return null;

		return $this->_store[$from];
	}
}

