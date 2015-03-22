<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	
	$bracketId = 0;
	if(isset($_GET['matchId']))
	{
		$bracketId = $_GET['matchId'];
		
		$bracket = $BRACKET_DATA_BO->getBracket($bracketId);
		
		$pagetitle = $bracket['MatchName'] . ' Bracket Scoring Breakdown';
	}
	
	$userId = $CURRENT_USER["user_id"];
	if(isset($_GET['userId']))
	{
		$userId = $_GET['userId'];
		
		$user = $USER_DATA_BO->getUser($userId);
		$pagetitle = $pagetitle . " - " . $user['UserName'];
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
		<div id="orders-datatable_wrapper" class="dataTables_wrapper">
			
			<div class="alert alert-info" role="alert">
				Below is the point-by-point breakdown of this bracket. Rows highlighted in green had the correct team and the correct placement. Rows in blue had the wrong placement selected.
				<br/><br/>
				The Potential Points column lists two possible scores for that row. The first number is the points scored if the correct team and placement are selected, the second number is the points if the team is correct, but the placement is wrong.
		    </div>
			
				<?php
				$scores =  $USER_DATA_BO->getScoringDetails($userId, $bracketId);
				$currRound = null;
				$currDivision = null;
				$totalPoints = 0;
				$divPoints = 0;
				
				foreach($scores as $score)
				{	
					if($currRound !=  $score['CompetitionRoundName'] || $currDivision != $score['DivisionName']) 
					{
						if( isset($currRound) && isset($currDivision))
						{
							?>
							<tfoot>
								<td colspan="4" style="text-align:right;">Total:</td>
								<td style="text-align:right;"><?php echo $divPoints ?></td>
							</tfoot>
							</table>
							<?php
							$divPoints = 0;
						}
					}
					
					if($currRound !=  $score['CompetitionRoundName']) 
					{	
						echo '<h3>' . $score['CompetitionRoundName'] . '</h3>';
						$currDivision = '';
					}
					
					if($currDivision != $score['DivisionName'])
					{	
						$tbtext = '';
						if ($score['TieBreakOrder'] != "0") 
						{
							$tbtext = '&nbsp;<small>(Tie Breaker)</small>';
						}
						
						echo '<h4>' . $score['DivisionName'] . $tbtext . '</h4>';
						
						?>
						<table id="orders-datatable" class="table dataTable table-condensed">
							<thead>
								<tr>
									<th></th>
									<th>Your Choice</th>
									<th>Actual Team</th>
									<th>Potential<br/>Points</th>
									<th style="text-align:right;">Points</th>
								</tr>
							</thead>
						<?php
					}
					
					$cellCss = '';
					if($score["CorrectPosition"] == 'Y')
					{
						$cellCss = 'success';
					}
					else if ($score["EntryInWinners"] == 'Y')
					{
						$cellCss = 'info';
					}
					
					$potentialPoints = $score["ScoreRightPosition"] . "/" . $score["ScoreWrongPosition"];
					
					echo '<tr class="' . $cellCss . '">';
					echo "<td>" . $score["Position"] . ".</td>";
					echo "<td>" . $score["EntryTeamName"] . "</td>";
					echo "<td>" . $score["TeamName"] . "</td>";
					echo '<td style="text-align:center;">' .  $potentialPoints . "</td>";
					echo '<td style="text-align:right;">' . $score["Score"] . "</td>";
					echo "</tr>";
					
					$currRound = $score['CompetitionRoundName'];
					$currDivision = $score["DivisionName"];
					$divPoints = $divPoints + $score["Score"];
					$totalPoints = $totalPoints = $score["Score"];
				}
				?>
				<tfoot>
					<td colspan="4" style="text-align:right;">Total:</td>
					<td style="text-align:right;"><?php echo $divPoints ?></td>
				</tfoot>
			</table>
		</div>
	</div>
</div>



<?php include $root . '/worldsbracket/footer.php'; ?>	

