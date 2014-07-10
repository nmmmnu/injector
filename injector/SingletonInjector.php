<?
namespace injector;


/**
 * Handle Dependency Injections in Singleton-like clases.
 *
 */
class SingletonInjector implements AbstractInjector{
	private $_singletons = array();
	private $_injector;


	/**
	 * constructor
	 *
	 * @param Injector $injector injector to be used
	 *
	 */
	function __construct(Injector $injector){
		$this->_injector = $injector;
	}


	function & specifications(){
		return $this->_injector->specifications();
	}


	function provide($classname){
		if (! isset($this->_singletons[$classname]))
			$this->_singletons[$classname] = $this->_injector->provide($classname);

		return $this->_singletons[$classname];
	}


	function callMethod($classname, $method){
		$instance = $this->provide($classname);

		return $this->_injector->callMethod($instance, $method);
	}


	static function test(){
		$conf = new Configuration();
		$conf->bind("host", new BindValue("localhost"));
		$conf->bind("port", new BindValue(80));

		$injector = new Injector( /* array($conf) */ );

		$injector->specifications()["conf"] = $conf;

		$classname = __NAMESPACE__ . "\\tests\\TestClass";

		$sInjector = new \injector\SingletonInjector($injector);


		$classname = __NAMESPACE__ . "\\tests\\TestClass";


		$bla1 = $sInjector->provide($classname);
		$bla2 = $sInjector->provide($classname);

		// must provide same object
		assert($bla1 === $bla2);

		// must provide same object
		assert($sInjector->callMethod($classname, "getID") == $sInjector->callMethod($classname, "getID"));
	}
}


