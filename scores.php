<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	
	$bracketId = 0;
	if(isset($_GET['id']))
	{
		$bracketId = $_GET['id'];
		
		$bracket = $BRACKET_DATA_BO->getBracket($bracketId);
		
		$pagetitle = $bracket['MatchName'] . ' Bracket Scores';
	}
	
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
				<div class="users-list">
					<div class="row user">
				<?php
					
					$scores =  $USER_DATA_BO->rankBracketScores($bracketId, 0);
					
					?>
					    <div>
					      <div class="barchart">
							<table class="table score-table">
								<tr>
									<th></th>
									<th></th>
									<th>User</th>
									<th>Score</th>
									<th>Tie Break 1</th>
									<th>Tie Break 2</th>
								</tr>
					       	 	<?php
								foreach($scores as $s)
								{
									?>
									<tr>
										<td><?php echo $s['Rank'] . "." ?></td>
										<td><div class="avatar"><img class="avatar" src="<?php echo $USER_DATA_BO->getUserAvatar($s['UserId']) ?>" /></div></td>
										<td>
											<a href="<?php echo 'score-breakdown.php?userId=' . $s['UserId'] . '&matchId=' . $bracketId ?>">
												<?php echo $s['UserName'] ?>
											</a>
										</td>
										<td><?php echo $s['Score'] ?></td>
										<td><?php echo $s['TieBreak1Score'] ?></td>
										<td><?php echo $s['TieBreak2Score'] ?></td>
									</tr>
									<?php
								}	
					       	 	?>
							</table>
					      </div>
					    </div>
					</div>
				</div>
				<?php
				?>
			</div>
		</div>
	</div>

<?php include $root . '/worldsbracket/footer.php'; ?>	

