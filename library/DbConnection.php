<?php
class DbConnectection
{
	private $hostname = "";
	private $username = "";
	private $password = "";
	private $dbname = "";
	
	protected $connection;
	
	function __construct($hostname, $username, $password, $dbname) {
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->dbname = $dbname;
	}

    public function connect()
    {
        $this->connection = new PDO("mysql:host=" . $this->hostname . ";dbname=". $this->dbname, $this->username, $this->password );
    }
	
	public function getPDO()
	{
		return $this->connection;
	}
}


?>