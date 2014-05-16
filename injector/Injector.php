<?
namespace injector;


use \ReflectionClass;
use \ReflectionMethod;


class Injector{
	const constructor = "__construct";


	private $_specifications;
	private $_singletons = array();


	function __construct(array $specifications = array()){
		$this->_specifications = $specifications;
	}


	function provide($classname){
		$args = array();
		foreach(self::getDependencyRequirements($classname) as $arg){
			$args[] = $this->getDependency($arg);
		}

		$instance = self::call__construct($classname, $args);

		return $instance;
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


	private function getDependencyFromSingleton(InjectorSpec $provider, $name){
		if (isset($this->_singletons[$name]))
			return $this->_singletons[$name];

		$value = $this->getDependencyFromProvider($provider);

		$this->_singletons[$name] = $value;

		return $value;
	}


	private function getDependencyFromProvider(InjectorSpec $provider){
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

