<?

interface Database{
	function db_whoami();
}

interface Queue{
	function q_whoami();
}

interface CreditCardProcessor{
	function cc_whoami();
}

// =============================================

class MySQLDatabase implements Database{
	private $_rand;
	private $_url;

	function __construct($mysqlhost, $mysqlport){
		$this->_rand = rand(0, 9999);
		$this->_url   = "mysql://$mysqlhost:$mysqlport/";
	}

	function db_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
		printf("\tDatabase URL: %s\n", $this->_url);
	}
}

class OracleDatabase implements Database{
	private $_rand;
	private $_url;

	function __construct($oraclehost, $oracleport){
		$this->_rand = rand(0, 9999);
		$this->_url   = "oracle://$oraclehost:$oracleport/";
	}

	function db_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
		printf("\tDatabase URL: %s\n", $this->_url);
	}
}

class OfflineQueue implements Queue{
	private $_rand;
	private $_database;

	function __construct(Database $database){
		$this->_database = $database;
		$this->_rand = rand(0, 9999);
	}

	function q_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
		$this->_database->db_whoami();
	}
}

class FakeCardProcessor implements CreditCardProcessor{
	private $_rand;
	private $_queue;
	private $_url;

	function __construct(Queue $queue){
		$this->_queue = $queue;
		$this->_url   = "http://development/";
		$this->_rand  = rand(0, 9999);
	}

	function cc_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
		printf("\tProcessor URL: %s\n", $this->_url);
		$this->_queue->q_whoami();
	}
}

class VisaCreditCardProcessor implements CreditCardProcessor{
	private $_rand;
	private $_queue;
	private $_url;

	function __construct(Queue $queue, $cchost, $ccport){
		$this->_queue = $queue;
		$this->_url   = "http://$cchost:$ccport/";
		$this->_rand  = rand(0, 9999);
	}

	function cc_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
		printf("\tProcessor URL: %s\n", $this->_url);
		$this->_queue->q_whoami();
	}
}

// =============================================

class Customer{
	private $_processor;
	private $_queue;

	function __construct(CreditCardProcessor $processor, Queue $queue){
		$this->_processor = $processor;
		$this->_queue     = $queue;
	}

	function cust_whoami(){
		echo __CLASS__ . "\n";
		echo $this->_processor->cc_whoami();
		echo $this->_queue->q_whoami();
	}
}

// =============================================

require_once __DIR__ . "/../__autoload.php";

// =============================================

$ns = __NAMESPACE__ . "\\";



$specs_development = new injector\InjectorConfiguration();
$specs_development->bind("database",	new injector\InjectorBindObject($ns . "MySQLDatabase"));
$specs_development->bind("mysqlhost",	new injector\InjectorBindValue("localhost"));
$specs_development->bind("mysqlport",	new injector\InjectorBindValue("3306"));

$specs_development->bind("queue",	new injector\InjectorBindObject($ns . "OfflineQueue", false));

$specs_development->bind("processor",	new injector\InjectorBindObject($ns . "FakeCardProcessor"));



$specs_production = new injector\InjectorConfiguration();
$specs_production->bind("database",	new injector\InjectorBindObject($ns . "OracleDatabase"));
$specs_production->bind("oraclehost",	new injector\InjectorBindValue("oracle.server1"));
$specs_production->bind("oracleport",	new injector\InjectorBindValue("5555"));

$specs_production->bind("queue",	new injector\InjectorBindObject($ns . "OfflineQueue", false));

$specs_production->bind("processor",	new injector\InjectorBindObject($ns . "VisaCreditCardProcessor"));
$specs_production->bind("cchost",	new injector\InjectorBindValue("www.citibank.com"));
$specs_production->bind("ccport",	new injector\InjectorBindValue("8080"));



$specs = $specs_development;
$specs = $specs_production;



$injector = new injector\Injector(array($specs));
$customer = $injector->provide("Customer");

//var_dump($customer);

$customer->cust_whoami();


