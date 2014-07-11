<?
require_once __DIR__ . "/../__autoload.php";


function formater($level, $class, $id){
	$sp = str_repeat("> ", $level);
	printf("%-8s %-30s %04d\n", $sp, $class, $id);
}

// =============================================

interface Database{
	function db_whoami($level);
}

interface Queue{
	function q_whoami($level);
}

interface CreditCardProcessor{
	function cc_whoami($level);
}

// =============================================

class MySQLDatabase implements Database{
	private $_rand;
	private $_url;

	function __construct($mysqlhost, $mysqlport){
		$this->_rand = rand(0, 9999);
		$this->_url   = "mysql://$mysqlhost:$mysqlport/";
	}

	function db_whoami($level){
		formater($level, __CLASS__, $this->_rand);
		printf("\t\tDatabase URL: %s\n", $this->_url);
	}
}

class OracleDatabase implements Database{
	private $_rand;
	private $_url;

	function __construct($oraclehost, $oracleport){
		$this->_rand = rand(0, 9999);
		$this->_url   = "oracle://$oraclehost:$oracleport/";
	}

	function db_whoami($level){
		formater($level, __CLASS__, $this->_rand);
		printf("\t\tDatabase URL: %s\n", $this->_url);
	}
}

class OfflineQueue implements Queue{
	private $_rand;
	private $_database;

	function __construct(Database $database){
		$this->_database = $database;
		$this->_rand = rand(0, 9999);
	}

	function q_whoami($level){
		formater($level, __CLASS__, $this->_rand);
		$this->_database->db_whoami($level + 1);
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

	function cc_whoami($level){
		formater($level, __CLASS__, $this->_rand);
		printf("\t\tProcessor URL: %s\n", $this->_url);
		$this->_queue->q_whoami($level + 1);
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

	function cc_whoami($level){
		formater($level, __CLASS__, $this->_rand);
		printf("\t\tProcessor URL: %s\n", $this->_url);
		$this->_queue->q_whoami($level + 1);
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
		echo $this->_processor->cc_whoami(1);
		echo $this->_queue->q_whoami(1);
	}
}


// =============================================


function getConfigurationDevelopment(){
	$ns = __NAMESPACE__ . "\\";

	$conf = new injector\Configuration();

	$conf->bind("database",		new injector\BindObject($ns . "MySQLDatabase"));
	$conf->bind("mysqlhost",	new injector\BindValue("localhost"));
	$conf->bind("mysqlport",	new injector\BindValue("3306"));

	$conf->bind("queue",		new injector\BindObject($ns . "OfflineQueue", false));

	$conf->bind("processor",	new injector\BindObject($ns . "FakeCardProcessor"));

	return $conf;
}

function getConfigurationProduction(){
	$ns = __NAMESPACE__ . "\\";

	$conf = new injector\Configuration();

	$conf->bind("database",		new injector\BindObject($ns . "OracleDatabase"));
	$conf->bind("oraclehost",	new injector\BindValue("oracle.server1"));
	$conf->bind("oracleport",	new injector\BindValue("5555"));

	$conf->bind("queue",		new injector\BindObject($ns . "OfflineQueue", false));

	$conf->bind("processor",	new injector\BindObject($ns . "VisaCreditCardProcessor"));
	$conf->bind("cchost",		new injector\BindValue("www.citibank.com"));
	$conf->bind("ccport",		new injector\BindValue("8080"));

	return $conf;
}

function demoApp($msg, injector\Injector $injector){
	echo "\n";
	echo "\n";
	echo "===========================================\n";
	echo "Now displaying $msg configuration\n";
	echo "===========================================\n";
	echo "\n";

	$customer = $injector->provide("Customer");

	//var_dump($customer);

	$customer->cust_whoami();
}

$conf = getConfigurationDevelopment();
$injector = new injector\Injector(array($conf));

demoApp("Development", $injector);
demoApp("Development again", $injector);

$conf = getConfigurationProduction();
$injector = new injector\Injector(array($conf));

demoApp("Production", $injector);


