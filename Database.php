<?php

class Database
{
	public $remote = array(
	    'host' => 'your_database_ip,port',
	    'user' => 'username',
	    'pass' => 'password',
	    'db' => 'dbname',
	    'dbtype' => 'mssql'
	);

    public $official = array(
        'host' => 'your_database_ip,port',
        'user' => 'username',
        'pass' => 'password',
        'db' => 'dbname',
        'dbtype' => 'mssql'
    );

	private static $_singleton;
    private $dbh;

    public function __construct() {
    	if($_SERVER['HTTP_HOST']==='your.domain.name')
            $creds = $this->official;//正式機 
        else
            $creds = $this->remote;//測試機

    	
    	$this->_connect(
        	$creds['host'],
        	$creds['user'],
        	$creds['pass'],
        	$creds['db'],
        	$creds['dbtype']
    	);
    	// set error level to warnings
    	$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    	
    	    	
    	$this->JDEDBFRONT = "JDE_CRP.CRPDTA"; 				//測試機

		$this->GCRDBFRONT = "TEST.dbo"; 	//測試機
		
		$this->CPSPAPIURL = "http://your.domain.com"; 	//測試機

    }
    
    public static function getInstance() {
        if(!self::$_singleton) {
            self::$_singleton = new Database();
        }
        return self::$_singleton;
    }
	
	private function _connect($host, $user, $pass, $db, $dbtype) {    	
    	try {
    		if($dbtype=='mysql')
        		$this->dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        	else
        		$this->dbh = new PDO("dblib:host=jdemssql;dbname=$db", $user, $pass);
        		//$this->dbh = new PDO("dblib:host=$host;dbname=$db", $user, $pass);
        		
        	$this->dbh->query('SET NAMES UTF8');
        	return true;
    	} catch (PDOException $e) {
        	return $e->getMessage();
    	}
    }
	
	public function getDBH(){
		return $this->dbh;
	}
}
