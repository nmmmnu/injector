<?
namespace injector;

/**
 * InjectorBind interface
 *
 */
interface Bind{
	/**
	 * provide the resource
	 *
	 * @return mixed
	 */
	function provide();


	/**
	 * check if resource must be singleton
	 *
	 * singleton resources are instanciated only once,
	 * but this is handled outside of the class implementing the interface
	 *
	 * @return boolean
	 */
	function isSingleton();


	/**
	 * check if resource needs to be re-evaluated
	 *
	 * re-evaluation means that resource is name of class,
	 * that needs to be instantiatad and eventually need some more dependencies
	 *
	 * @return boolean
	 */
	function isFinal();
}

