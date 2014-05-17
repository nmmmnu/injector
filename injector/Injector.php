<?
namespace injector;


use \ReflectionClass;
use \ReflectionMethod;

/**
 * Dependency Injection
 *
 * Inject dependency into:
 * - constructor, e.g. instanciate new class
 * - method, e.g. call the method of some object
 */
class Injector{
	const constructor = "__construct";


	private $_specifications;
	private $_singletons = array();


	/**
	 * constructor
	 *
	 * construct new Injector
	 *
	 * @param array $specifications array of InjectorConfiguration objects
	 *
	 */
	function __construct(array $specifications = array()){
		$this->_specifications = $specifications;
	}

	/**
	 * Lazi constructor call,
	 *
	 * e.g. instanciate new object
	 *
	 * @param string $classname name of the class
	 *
	 */
	function provide($classname){
		$args = array();
		foreach(self::getDependencyRequirements($classname) as $arg){
			$args[] = $this->getDependency($arg);
		}

		$instance = self::call__construct($classname, $args);

		return $instance;
	}


	/**
	 * Lazi method call
	 *
	 * @param object $instance instance of a class
	 * @param string $method method to be called
	 *
	 */
	function callMethod($instance, $method){
		$args = array();
		foreach(self::getDependencyRequirements($instance, $method) as $arg){
			$args[] = $this->getDependency($arg);
		}

		return call_user_func_array( array($instance, $method), $args);
	}


	private function getDependency($name){
		foreach($this->_specifications as $injectorSpecs){
			$provider = $injectorSpecs->get($name);

			if ($provider === null)
				continue;

			if ($provider->isSingleton())
				return $this->getDependencyFromSingleton($provider, $name);

			return $this->getDependencyFromProvider($provider);
		}
	}


	private function getDependencyFromSingleton(Bind $provider, $name){
		if (isset($this->_singletons[$name]))
			return $this->_singletons[$name];

		$value = $this->getDependencyFromProvider($provider);

		$this->_singletons[$name] = $value;

		return $value;
	}


	private function getDependencyFromProvider(Bind $provider){
		$value = $provider->provide();

		if ($provider->isFinal())
			return $value;

		// tail recursion
		return $this->provide($value);
	}


	private static function call__construct($classname, array $args){
		$reflection  = new ReflectionClass($classname);

		return $reflection->newInstanceArgs($args);
	}


	private static function getDependencyRequirements($classname, $classmethod = self::constructor){
		$reflection = new ReflectionMethod($classname, $classmethod);

		$params = array();
		foreach($reflection->getParameters() as $param)
			$params[] = $param->name;

		return $params;
	}
}

