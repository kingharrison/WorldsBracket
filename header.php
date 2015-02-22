<html>
	<head>
<?php 

	ini_set('display_errors', 'On');

	$root = $_SERVER['DOCUMENT_ROOT'];
  	$worlds_bracket_home = $root . '/worldsbracket/';
	require_once($worlds_bracket_home . "library/include.php");
	
	// set up avatar
	$avatarPath = "http://board.fierce-brands.com/styles/fierceboardlogo.png";
	if(isset($CURRENT_USER))
	{
		$avatarPath = "http://board.fierce-brands.com/data/avatars/s/" . intval($CURRENT_USER['user_id']/1000) . "/" . $CURRENT_USER['user_id'] . ".jpg";
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
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js"></script>-->
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
	
	<style>
		#dashboard .chart {
			margin-bottom:20px !important;
		}
		.ui-autocomplete {
			max-height: 150px;
			overflow-y: auto;
			/* prevent horizontal scrollbar */
			overflow-x: hidden;
			font-size:.8em;
		}
		* html .ui-autocomplete {
			height: 150px;
		}
	
		.ui-front {
			z-index: 100000 !important;
		}
	</style>


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
						<?php echo $CURRENT_USER['username'] ?>
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
							$res =  $BRACKET_DATA_BO->getAllBrackets(2015);
							foreach($res as $row)
							{
								echo '<li><a href="bracket.php?id=' . $row['MatchId'] . '">' . $row['MatchName']. '</a></li>';
							}
							
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
		if($CURRENT_USER['is_staff'] == 1)
		{
			
		?>
			
			<div class="menu-section">
				<h3>Admin</h3>
				<ul>
					<!--
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
					</li>-->
					<li>
						<a href="#" data-toggle="sidebar">
							<i class="ion-settings"></i> <span>Worlds Winners</span>
							<i class="fa fa-chevron-down"></i>
						</a>
						<ul class="submenu">
							<ul class="submenu">
								<?php
								$res =  $BRACKET_DATA_BO->getAllDivisions();
								foreach($res as $row)
								{
									echo '<li><a href="admin-winners.php?id=' . $row['DivisionId'] . '">' . $row['DivisionName']. '</a></li>';
								}
						
								?>
							</ul>
						</ul>
					</li>
				</ul>
			</div>
		<?php
		// end $CURRENT_USER['is_staff'] == 1
		}	
		?>	
		
			<div class="bottom-menu hidden-sm">
				<ul>
					
				</ul>
			</div>
		</div>

		
			

