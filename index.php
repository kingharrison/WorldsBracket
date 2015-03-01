<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	

?>

		<div id="content">
			<div class="menubar">
				<div class="sidebar-toggler visible-xs">
					<i class="ion-navicon"></i>
				</div>

				<div class="page-title">
					<?php echo $pagetitle ?>
				</div>
			</div>
			<div class="content-wrapper">

<?php
if (!isset($CURRENT_USER)) {
	echo '<div class="alert alert-warning" role="alert">Please <a href="' . $config['fierceboardUrl'] . '/login/">first log into fierceboard</a> to take part in the bracket contest</div>';
}
else
{
	?>
	<div class="charts clearfix">
		<div class="chart">
			<h3>Welcome!</h3>
			
			Welcome to the fierceboard bracket contest! There are two brackets - coed and all-girl, with one winner each. Entries will open April 1 and close April 24. Scores will be updated after each day of worlds and a winner will be crowned on finals day!
			<br/><br/>
			Good luck!
			
		
	    </div>
	    <div class="chart">
		<h3>Your Bracket Status</h3>
		
		<?php
		$status = $USER_DATA_BO->getBracketStatus($CURRENT_USER["user_id"], $config_season);
		foreach($status as $m)
		{
			$percent = intval(($m['MyEntries'] / $m['NumEntries']) * 100);
			if($percent == 100) {
				$statusClass = 'success';
			}
			else if ($percent == 0) {
				$statusClass = 'danger';
			}
			else {
				$statusClass = 'warning';
			}
			
			?>
			<div class="referral">
				<span>
					<a href="bracket.php?id=<?php echo $m['MatchId'] ?>">
						<?php echo $m['MatchName'] . " Bracket" ?>
					</a>
				</span>
	            <div class="progress">
	                <div class="progress-bar progress-bar-<?php echo $statusClass ?>" role="progressbar" aria-valuenow="<?php echo $percent ?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width: <?php echo $percent ?>%">
						<?php echo $percent ?>%
	                </div>
	            </div>
		  	</div>
			<?php
		}
		?>
		
	    </div>
	  </div>
	  
	
	<?php
}	
?>


<?php include $root . '/worldsbracket/footer.php'; ?>	

