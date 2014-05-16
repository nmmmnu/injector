<?
namespace injector;

/**
 * InjectorConfiguration
 *
 * configuration placeholder for Injector class
 *
 */
class InjectorConfiguration{
	private $_store = array();

	/**
	 * bind provider to a name
	 *
	 * @param string $from binding name
	 * @param InjectorBind $provider provider
	 *
	 */
	function bind($from, InjectorBind $provider){
		$this->_store[$from] = $provider;
	}


	/**
	 * get provider for a name
	 *
	 * @param string $from binding name
	 * @return InjectorBind|null provider or null if no such binding
	 *
	 */
	function get($from){
		if (! isset($this->_store[$from]))
			return null;

		return $this->_store[$from];
	}
}

