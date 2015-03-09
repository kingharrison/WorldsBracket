<?php
class UserData
{
	protected $connection;
	
	function __construct($connection) {
		$this->connection = $connection;
	}
	
	public function getUser($userId) {
		$stmt = $this->connection->prepare('SELECT * FROM Users WHERE FbUserId = :userid');
		$stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	public function getUserAvatar($userId) {
		return "http://board.fierce-brands.com/data/avatars/s/" . intval($userId/1000) . "/" . $userId . ".jpg";
	}
	
	public function logUserVisit($userId, $userName)
	{
		$user = $this->getUser($userId);
		
		date_default_timezone_set("America/New_York");
		
		$dt = new DateTime();
		$dt->setTimestamp(time());
		$currenttime = $dt->format('Y-m-d H:i:s');
		
		
		if(isset($user['UserName']))
		{	
			$stmt = $this->connection->prepare('UPDATE Users SET UserName = :username, LastVisitDate = :curtime WHERE FbUserId = :userid');
			$stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
			$stmt->bindParam(':username', $userName, PDO::PARAM_STR);
			$stmt->bindParam(':curtime', $currenttime, PDO::PARAM_STR);
		
			$stmt->execute();
		}
		else
		{	
			$stmt = $this->connection->prepare('INSERT INTO Users(FbUserId, UserName, FirstVisitDate, LastVisitDate) VALUES(:userid, :username, :curtime, :curtime)');
			$stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
			$stmt->bindParam(':username', $userName, PDO::PARAM_STR);
			$stmt->bindParam(':curtime', $currenttime, PDO::PARAM_STR);
		
			$stmt->execute();
		}
	}
	
	private function getBracketScores($bracketId, $scoreLimit)
	{
		$sTop = '';
		if($scoreLimit > 0)
		{
			$sTop = ' LIMIT 0, ' . $scoreLimit;
		}
		
		
		$stmt = $this->connection->prepare('SELECT  * FROM VW_Scores
											WHERE MatchId = :match
											ORDER BY Score DESC, TieBreak1Score DESC, TieBreak2Score DESC, 
											TieBreak3Score DESC, TieBreak4Score DESC, TieBreak5Score DESC' 
											. $sTop);
		$stmt->bindParam(':match', $bracketId, PDO::PARAM_INT);
		$stmt->execute();
	
		return $stmt->fetchAll();
	}
	
	public function rankBracketScores($bracketId, $scoreLimit)
	{
		$scores =  $this->getBracketScores($bracketId, $scoreLimit);
		
		$prevScore = null;
		$prevTb1 = null;
		$prevTb2 = null;
		$prevTb3 = null;
		$prevTb4 = null;
		$prevTb5 = null;
		$currRank = 0;
		
		$numTies = 0;
		
		foreach($scores as &$field)
		{
			if($field['Score'] == $prevScore && $field['TieBreak1Score'] == $prevTb1 && 
				$field['TieBreak2Score'] == $prevTb2 && $field['TieBreak3Score'] == $prevTb3 && 
					$field['TieBreak4Score'] == $prevTb4 && $field['TieBreak5Score'] == $prevTb5)
			{
		        // increase the tie counter
				$numTies = $numTies + 1;
		    }
			else
			{
				// increment by the number of ties so score is 1, 2, 2, 4
				$currRank = $currRank + 1 + $numTies;
				
				// reset tie ranking
				$numTies = 0;
				
				// store all the new fields
				$prevScore = $field['Score'];
				$prevTb1 = $field['TieBreak1Score'];
				$prevTb2 = $field['TieBreak2Score'];
				$prevTb3 = $field['TieBreak3Score'];
				$prevTb4 = $field['TieBreak4Score'];
				$prevTb5 = $field['TieBreak5Score'];
			}
			
			$field['Rank'] = $currRank;
		}
		
		return $scores;
		
	}
	
	public function getScoringDetails($userId, $matchId)
	{
		$stmt = $this->connection->prepare('SELECT *
											FROM VW_ScoringDetails
											WHERE MatchId = :match AND UserId = :user
											ORDER BY  CompetitionRoundId, DivisionId, EntryPosition');
		$stmt->bindParam(':match', $matchId, PDO::PARAM_INT);
		$stmt->bindParam(':user', $userId, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getBracketStatus($userId, $season)
	{
		$stmt = $this->connection->prepare('SELECT MatchId, MatchName, SUM(NumEntries) AS NumEntries
											FROM VW_Matches vwm
											WHERE Season = :season
											GROUP BY MatchId, MatchName');
		$stmt->bindParam(':season', $season, PDO::PARAM_INT);
		$stmt->execute();
	
		$totals = $stmt->fetchAll();
		
		
		foreach($totals as $t)
		{
			
			$stmt2 = $this->connection->prepare('SELECT COUNT( * ) as EntryCnt
												FROM BracketEntry
												WHERE matchid = :match and UserId = :user');
			$stmt2->bindParam(':match', $t['MatchId'], PDO::PARAM_INT);
			$stmt2->bindParam(':user', $userId, PDO::PARAM_INT);
			$stmt2->execute();
			
			$res = $stmt2->fetch();
			
			$t['MyEntries'] = $res['EntryCnt'];
			$newresult[] = $t;
		}
		return $newresult;
	}
	
}
?>