<html>
	<head>
<?php 

	ini_set('display_errors', 'On');

	$root = $_SERVER['DOCUMENT_ROOT'];
  	$worlds_bracket_home = $root . '/worldsbracket/';
		
	include_once($worlds_bracket_home . "library/userinfo.php");
	include_once($worlds_bracket_home . "library/BracketConfig.php");
	require_once($worlds_bracket_home . "config.php");

	
	// set up database connection 
	$config_db_username = $config['dbUserName'];
	$config_db_password = $config['dbPassword'];
	$config_db_hostname = $config['dbHostName'];
	$mysqli = new mysqli($config_db_hostname, $config_db_username, $config_db_password, "ashley");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	// get the user from session
	$currentUser = SessionData::getCurrentUser();
	$avatarPath = "http://board.fierce-brands.com/styles/fierceboardlogo.png";
	if(isset($currentUser))
	{
		$avatarPath = "http://board.fierce-brands.com/data/avatars/s/" . intval($currentUser['user_id']/1000) . "/" . $currentUser['user_id'] . ".jpg";
	}
	
?>

	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />	
	<title>Fierceboard Bracket Contest</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- stylesheets -->
	<link rel="stylesheet" type="text/css" href="css/compiled/theme.css" />
	<link rel="stylesheet" type="text/css" href="css/vendor/animate.css" />
	<link rel="stylesheet" type="text/css" href="css/vendor/brankic.css" />
	<link rel="stylesheet" type="text/css" href="css/vendor/ionicons.min.css" />
	<link rel="stylesheet" type="text/css" href="css/vendor/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="css/vendor/datepicker.css" />
	<link rel="stylesheet" type="text/css" href="css/vendor/morris.css" />
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.css" />

	<!-- javascript -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js"></script>
	<script src="js/bootstrap/bootstrap.min.js"></script>
	<script src="js/vendor/jquery.cookie.js"></script>
	<script src="js/vendor/moment.min.js"></script>
	<script src="js/theme.js"></script>
	<script src="js/vendor/bootstrap-datepicker.js"></script>
	<script src="js/vendor/raphael-min.js"></script>
	<script src="js/vendor/morris.min.js"></script>

	<script src="js/vendor/jquery.flot/jquery.flot.js"></script>
	<script src="js/vendor/jquery.flot/jquery.flot.time.js"></script>
	<script src="js/vendor/jquery.flot/jquery.flot.tooltip.js"></script>


	<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body id="dashboard"><div id="wrapper">
		<div id="sidebar-default" class="main-sidebar">
			<div class="current-user">
				<a href="index.html" class="name">
					<img class="avatar" src="<?php echo $avatarPath ?>" />
					<span>
						<?php echo $currentUser['username'] ?>
						<!--<i class="fa fa-chevron-down"></i>-->
					</span>
				</a>
				<!--<ul class="menu">
					<li>
						<a href="http://board.fierce-brands.com/">Return to Fierceboard</a>
					</li>
				</ul>-->
			</div>
			<div class="menu-section">
				<h3>General</h3>
				<ul>
					<li>
						<a href="index.php" title="Home"> <!-- class="active" -->
							<i class="ion-home"></i> 
							<span>Home</span>
						</a>
					</li>
					<li>
						<a href="users.html" data-toggle="sidebar">
							<i class="ion-flash"></i> <span>Brackets</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<?php
								$res = $mysqli->query("SELECT * FROM BracketMatch");
								while($row = $res->fetch_assoc()){
									echo '<li><a href="bracket.php?id=' . $row['MatchId'] . '">' . $row['MatchName']. '</a></li>';
								}
								
								$res->close();
							?>
							
						</ul>
					</li>
					<li>
						<a href="http://board.fierce-brands.com/" title="Return to Fierceboard">
							<i class="ion-reply"></i> 
							<span>Return to Fierceboard</span>
						</a>
					</li>
				</ul>
			</div>
			<!--<div class="menu-section">
				<h3>Application</h3>
				<ul>
					<li>
						<a href="account.html" data-toggle="sidebar">
							<i class="ion-earth"></i> <span>App Pages</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<li><a href="sidebar.html">Inbox Messages</a></li>
							<li><a href="user-profile.html">User profile</a></li>
							<li><a href="latest-activity.html">Latest activity</a></li>
							<li><a href="projects.html">Projects</a></li>
							<li><a href="steps.html">Steps to launch</a></li>
							<li><a href="calendar.html">Calendar</a></li>
						</ul>
					</li>
					<li>
						<a href="account.html" data-toggle="sidebar">
							<i class="ion-card"></i> <span>Pricing</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<li><a href="pricing.html">Pricing (Plans)</a></li>
							<li><a href="pricing-alt.html">Pricing charts</a></li>
							<li><a href="billing-form.html">Billing form</a></li>
							<li><a href="invoice.html">Invoice</a></li>
						</ul>
					</li>
					<li>
						<a href="account.html" data-toggle="sidebar">
							<i class="ion-flash"></i> <span>Features</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<li><a href="email-templates.html">Email templates</a></li>
							<li><a href="gallery.html">Gallery</a></li>
							<li><a href="ui.html">UI Extras</a></li>
							<li><a href="docs.html">API Documentation</a></li>
							<li><a href="signup.html">Sign up</a></li>
							<li><a href="signin.html">Sign in</a></li>
							<li><a href="status.html">App Status</a></li>
						</ul>
					</li>
				</ul>
			</div> -->
			
		<?php
		// show the admin section if user is staff
		if($currentUser['is_staff'] == 1)
		{
			
		?>
			
			<div class="menu-section">
				<h3>Admin</h3>
				<ul>
					<li>
						<a href="account.html" data-toggle="sidebar">
							<i class="ion-person"></i> <span>My account</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<li><a href="account-profile.html">Settings</a></li>
							<li><a href="account-billing.html">Billing</a></li>
							<li><a href="account-notifications.html">Notifications</a></li>
							<li><a href="account-support.html">Support</a></li>
						</ul>
					</li>
					<li>
						<a href="#" data-toggle="sidebar">
							<i class="ion-usb"></i> <span>Level Navigation</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="invoice.html" data-toggle="sidebar">
									Submenu
									<i class="fa fa-chevron-down"></i>
								</a>
								<ul class="submenu">
									<li><a href="#">Last menu</a></li>
									<li><a href="#">Last menu</a></li>
								</ul>
							</li>
							<li><a href="invoice.html">Menu link</a></li>
							<li><a href="#">Extra link</a></li>
						</ul>
					</li>
				</ul>
			</div>
		<?php
		// end $currentUser['is_staff'] == 1
		}	
		?>	
		
			<div class="bottom-menu hidden-sm">
				<ul>
					
				</ul>
			</div>
		</div>

		
			

