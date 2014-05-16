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

	function __construct(){
		$this->_rand = rand(0, 9999);
	}

	function db_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
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

class VisaCreditCardProcessor implements CreditCardProcessor{
	private $_rand;
	private $_queue;

	function __construct(Queue $queue){
		$this->_queue = $queue;
		$this->_rand = rand(0, 9999);
	}

	function cc_whoami(){
		printf("%-30s %04d\n", __CLASS__, $this->_rand);
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

$ns = ""; // __NAMESPACE__ . "\\";

$specs = new injector\InjectorSpecs();
$specs->bind("database",	new injector\InjectorSpecObject($ns . "MySQLDatabase"));
$specs->bind("queue",		new injector\InjectorSpecObject($ns . "OfflineQueue", false));
$specs->bind("processor",	new injector\InjectorSpecObject($ns . "VisaCreditCardProcessor"));


$injector = new injector\Injector(array($specs));
$customer = $injector->provide("Customer");

//var_dump($customer);

$customer->cust_whoami();


