<?php
//ini_set('display_errors', 'On');


define('XF_ROOT', '/home/kingharrison/fierceboard'); // set this (absolute path)!
define('TIMENOW', time());
define('SESSION_BYPASS', false); // if true: logged in user info and sessions are not needed

require_once(XF_ROOT . '/library/XenForo/Autoloader.php');
require_once(XF_ROOT . '/worldsbracket/library/SessionData.php');

XenForo_Autoloader::getInstance()->setupAutoloader(XF_ROOT . '/library');

XenForo_Application::initialize(XF_ROOT . '/library', XF_ROOT);
XenForo_Application::set('page_start_time', TIMENOW);
XenForo_Application::disablePhpErrorHandler();
XenForo_Application::setDebugMode(true);

if (!SESSION_BYPASS)
{
    XenForo_Session::startPublicSession();
    $visitor = XenForo_Visitor::getInstance();
	//var_dump($visitor);
	
    if ($visitor->getUserId())
    {
        $userModel = XenForo_Model::create('XenForo_Model_User');
        $userinfo = $userModel->getFullUserById($visitor->getUserId());
		//var_dump($userinfo);
		
		SessionData::setCurrentUser($userinfo);
    }
	else
	{
		SessionData::unsetCurrentUser();
	}
}
else 
{
	SessionData::unsetCurrentUser();
}


restore_error_handler();
restore_exception_handler();


?>