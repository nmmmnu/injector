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


function getConfigurationDevelopment(){
	$conf = new injector\Configuration();

	$conf->bind("database",	new injector\BindObject($ns . "MySQLDatabase"));
	$conf->bind("mysqlhost",	new injector\BindValue("localhost"));
	$conf->bind("mysqlport",	new injector\BindValue("3306"));

	$conf->bind("queue",	new injector\BindObject($ns . "OfflineQueue", false));

	$conf->bind("processor",	new injector\BindObject($ns . "FakeCardProcessor"));

	return $conf;
}

function getConfigurationProduction(){
	$conf = new injector\Configuration();

	$conf->bind("database",	new injector\BindObject($ns . "OracleDatabase"));
	$conf->bind("oraclehost",	new injector\BindValue("oracle.server1"));
	$conf->bind("oracleport",	new injector\BindValue("5555"));

	$conf->bind("queue",	new injector\BindObject($ns . "OfflineQueue", false));

	$conf->bind("processor",	new injector\BindObject($ns . "VisaCreditCardProcessor"));
	$conf->bind("cchost",	new injector\BindValue("www.citibank.com"));
	$conf->bind("ccport",	new injector\BindValue("8080"));

	return $conf;
}

function demoApp($msg, injector\Configuration $conf){
	echo "\n";
	echo "\n";
	echo "===========================================\n";
	echo "Now displaying $msg configuration\n";
	echo "===========================================\n";
	echo "\n";

	$injector = new injector\Injector(array($conf));

	$customer = $injector->provide("Customer");

	//var_dump($customer);

	$customer->cust_whoami();
}

demoApp("Development", getConfigurationDevelopment());

demoApp("Production", getConfigurationProduction());


