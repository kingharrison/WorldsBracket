<?php
require_once('/home/kingharrison/fierceboard/worldsbracket/config.php');

//var_dump($config);

class BracketConfig
{
	private $adminUserIds;
	private $submissionEndDate;
	private $compYear;
	
	public function __construct()
	{
		$this->$adminUserIds = explode(",", $config['adminUserIds']);
		
		
	}
	
	public function getAdminUserIds()
	{
		return $adminUserIds;
	}
	
}
	
	
	
	
?>