<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	
	$brackets =  $BRACKET_DATA_BO->getAllBrackets($config_season);
	
	// bracket start/end date
	$currentTime = new DateTime();
	$currentTime->setTimestamp(time());
	$startDate = new DateTime();
	$startDate->setTimestamp(strtotime($brackets[0]['StartDate']));
	$endDate = new DateTime();
	$endDate->setTimestamp(strtotime($brackets[0]['EndDate']));

?>
	<style type="text/css">
	.score-table td {
		vertical-align:middle !important;
	}
	
	.user {
		border: none !important;
	}
	
	</style>

	<div id="users">
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
	echo '<div class="alert alert-warning" role="alert">You must be <a href="' . $config['fierceboardUrl'] . '/login/">logged into fierceboard</a> to participate in the bracket contest</div>';
}

	?>
	<div class="charts clearfix">
		<div class="chart">
			<h3>Welcome!</h3>
			Welcome to the fierceboard bracket contest! There are two brackets - coed and all-girl, with one winner each. Entries will open April 13 and close April 24. Scores will be updated after each day of worlds and a winner will be crowned on finals day!
			<br/><br/>
			Good luck!
			<br/><br/>
			<h3>Prizes</h3>
			We're looking for sponsors for prizes. Contact <a href="mailto:king@fierceboard.com">King</a> if you're a business interested in offering a prize. You will get an advertisement in this spot.
	    </div>
		
		<?php
		
		if (isset($CURRENT_USER) && $currentTime < $endDate)
		{
			?>
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
	<?php
		}
	?>
	</div>
	<?php
	
	// ** popular teams **
	foreach($brackets as $bracket)
	{
		$teams =  $USER_DATA_BO->getBracketPopularity($bracket['MatchId'], 10);
		
		?>
	    <div class="col-sm-6">
	    	<div class="barchart">
	        	<h3><?php echo $bracket['MatchName'] ?> Bracket Favorites</h3>
				<ol>
					<?php
					foreach($teams as $t)
					{
						echo "<li>" . $t['TeamName']. "</li>";
					}	
					?>
				</ol>
			</div>
		</div>
			
		<?php
	}

	// ** Scores **
	
	if ($currentTime > $endDate)
	{	
		// stupid extra divs to make the css work
		echo '<div class="users-list"><div class="row user">';
		
		foreach($brackets as $bracket)
		{
			$scores =  $USER_DATA_BO->rankBracketScores($bracket['MatchId'], 10);
			
			?>
		    <div class="col-sm-6">
		      <div class="barchart">
		        <h3><?php echo $bracket['MatchName'] ?> Bracket Top 10</h3>
				<table class="table score-table">
					<thead>
						<tr>
							<th></td>
							<th></td>
							<th>User</td>
							<th>Score</td>
						</tr>
					</thead>
		       	 	<?php
					foreach($scores as $s)
					{
						
						?>
						<tr>
							<td><?php echo $s['Rank'] . "." ?></td>
							<td><div class="avatar"><img class="avatar" src="<?php echo $USER_DATA_BO->getUserAvatar($s['UserId']) ?>" /></div></td>
							<td>
								<a href="<?php echo 'score-breakdown.php?userId=' . $s['UserId'] . '&matchId=' . $bracket['MatchId'] ?>">
									<?php echo $s['UserName'] ?>
								</a>
							</td>
							<td><?php echo $s['Score'] ?></td>
						</tr>
						<?php
					}	
		       	 	?>
					<tfoot>
						<tr>
							<td colspan="4" style="text-align:right;">
							 	<a href="scores.php?id=<?php echo $bracket['MatchId'] ?>">See All</a>
							</td>
						</tr>
					</tfoot>
				</table>
		      </div>
		    </div>
		<?php
		}
		echo "</div></div>";
	}
	
?>
</div>

<?php include $root . '/worldsbracket/footer.php'; ?>	

