<?
namespace injector;


spl_autoload_register(
	function($class){
		$parts = explode("\\", $class);

		if ($parts[0] != __NAMESPACE__)
			return;

		$file = implode("/", $parts) . ".php";

		$file = dirname(__FILE__) . "/" . $file;

		//echo "Loading $file...\n";

		require_once $file;
	}
);

