<?
namespace injector;


/**
 * InjectorBind to Object in file
 *
 * the object is later re-evaluated
 *
 */
class BindFileObject implements Bind{
	private static $_data = array();

	private $_filename;
	private $_singleton;


	/**
	 * constructor
	 *
	 * @param string $classname name of the class
	 * @param boolean $singleton
	 *
	 */
	function __construct($filename, $singleton = true){
		$this->_filename  = self::checkPath($filename);
		$this->_singleton = $singleton;
	}


	function provide(){
		if (! $this->_filename)
			return null;

		return $this->getClassName();
	}


	function isSingleton(){
		return $this->_singleton;
	}


	function isFinal(){
		return false;
	}


	private static function checkPath($filename){
		$filename = realpath($filename);
		if ($filename === false)
			return false;

		if (!file_exists($filename))
			return false;

		return $filename;
	}


	private function getClassName(){
		if (isset(self::$_data[$this->_filename]))
			return self::$_data[$this->_filename];

		$classname = $this->getClassNameReal();

		if ($classname === false)
			return null;

		self::$_data[$this->_filename] = $classname;

		return $classname;
	}


	private function getClassNameReal(){
		$before = get_declared_classes();

		include_once $this->_filename;

		$after = get_declared_classes();

		foreach (array_diff($after, $before) as $classname)
			return $classname;

		return false;
	}

}

