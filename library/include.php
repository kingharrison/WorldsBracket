<?php

	$root = $_SERVER['DOCUMENT_ROOT'];
  	$worlds_bracket_home = $root . '/worldsbracket/';
		
	require_once($worlds_bracket_home . "config.php");
	require_once($worlds_bracket_home . "library/userinfo.php");
	require_once($worlds_bracket_home . "library/DbConnection.php");
	require_once($worlds_bracket_home . "library/BracketData.php");
	require_once($worlds_bracket_home . "library/UserData.php");

	
	$config_db_username = $config['dbUserName'];
	$config_db_password = $config['dbPassword'];
	$config_db_hostname = $config['dbHostName'];
	$config_db_name = $config['dbName'];
	$config_season = $config['compYear'];
	
	date_default_timezone_set("America/New_York");
	
	// set up database connection
	$connection = new DbConnectection($config_db_hostname, $config_db_username, $config_db_password, $config_db_name);
	$connection->connect();
	
	// set up global variables
	$CURRENT_USER = SessionData::getCurrentUser();
	$BRACKET_DATA_BO = new BracketData($connection->getPDO());
	$USER_DATA_BO = new UserData($connection->getPDO());
	
?>