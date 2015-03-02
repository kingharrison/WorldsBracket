<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	

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
		
		<?php
		$brackets =  $BRACKET_DATA_BO->getAllBrackets($config_season);
		
		// bracket start/end date
		$currentTime = new DateTime();
		$currentTime->setTimestamp(time());
		$startDate = new DateTime();
		$startDate->setTimestamp(strtotime($brackets[0]['StartDate']));
		$endDate = new DateTime();
		$endDate->setTimestamp(strtotime($brackets[0]['EndDate']));
		
		//if($currentTime < $endDate)
		//{
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
		  </div>

	<?php
	//}
	//else
	//{
		// stupid extra divs to make the css work
		echo '<div class="users-list"><div class="row user">';
		
		foreach($brackets as $bracket)
		{
			$scores =  $USER_DATA_BO->getBracketScores($bracket['MatchId']);
			
			?>
		    <div class="col-sm-6">
		      <div class="barchart">
		        <h3><?php echo $bracket['MatchName'] ?> Bracket Scores</h3>
				<table class="table score-table">
					<tr>
						<th></td>
						<th></td>
						<th>User</td>
						<th>Score</td>
					</tr>
		       	 	<?php
					$i = 0;
					foreach($scores as $s)
					{
						$i = $i+1;
						?>
						<tr>
							<td><?php echo $i . "." ?></td>
							<td><div class="avatar"><img class="avatar" src="<?php echo $USER_DATA_BO->getUserAvatar($s['UserId']) ?>" /></div></td>
							<td><?php echo $s['UserName'] ?></td>
							<td><?php echo $s['Score'] ?></td>
						</tr>
						<?php
					}	
		       	 	?>
				</table>
		      </div>
		    </div>
		<?php
		}
		echo "</div></div>";
	//}
}	
?>
	</div>

<?php include $root . '/worldsbracket/footer.php'; ?>	

