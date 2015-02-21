<?php
class SessionData
{

	public static function getCurrentUser()
	{
		
		//session_start();
		if(isset($_SESSION) && isset($_SESSION['currentUser']))
		{
			$user = $_SESSION['currentUser'];
			return $user;
		}
		else
		{
			return null;
		}
	}

	public static function setCurrentUser($userinfo)
	{
		//session_start();
	
		$_SESSION['currentUser'] = $userinfo;
	
		//session_write_close();
	
	}

	public static function unsetCurrentUser()
	{
		//session_start();
		if(isset($_SESSION))
		{
			unset($_SESSION['currentUser']);
		}
	
		//session_write_close();
	}

}
?>