<?php
class BracketData
{
	protected $connection;
	
	function __construct($connection) {
		$this->connection = $connection;
	}
	
	public function getAllRounds() {
		$stmt = $this->connection->prepare('SELECT * FROM CompetitionRound Order by CompetitionRoundId');
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getRound($roundId) {
		$stmt = $this->connection->prepare('SELECT * FROM CompetitionRound WHERE CompetitionRoundId = :round');
		$stmt->bindParam(':round', $roundId, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	public function getAllDivisions() {
		$stmt = $this->connection->prepare('SELECT * FROM Divisions');
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getDivision($divisionId) {
		$stmt = $this->connection->prepare('SELECT * FROM Divisions WHERE DivisionId = :div ORDER BY DivisionId');
		$stmt->bindParam(':div', $divisionId, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	public function getNextDivision($divisionId) {
		$stmt = $this->connection->prepare('SELECT * FROM Divisions WHERE DivisionId > :div ORDER BY DivisionId LIMIT 1');
		$stmt->bindParam(':div', $divisionId, PDO::PARAM_INT); 
		$stmt->execute();
		
		return $stmt->fetch();
	}

    public function getAllBrackets($season) {
		$stmt = $this->connection->prepare('SELECT * FROM BracketMatch WHERE season = :season');
		$stmt->bindParam(':season', $season, PDO::PARAM_INT); 
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getBracket($bracketId) {
		$stmt = $this->connection->prepare('SELECT * FROM BracketMatch WHERE matchid = :matchid');
		$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT); // <-- Automatically sanitized for SQL by PDO
		$stmt->execute();
		
		// return just one row
		return $stmt->fetch();
	}
	
	public function getBracketRounds($bracketId) {
		$stmt = $this->connection->prepare('SELECT MatchId, CompetitionRoundId, CompetitionRoundName
											FROM VW_Matches
											WHERE matchid = :matchid
											GROUP BY MatchId, CompetitionRoundId, CompetitionRoundName
											ORDER BY CompetitionRoundId');
		$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT); 
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getBracketDivisions($bracketId) {
		$stmt = $this->connection->prepare('SELECT * FROM VW_Matches WHERE matchid = :matchid ORDER BY TieBreakOrder');
		$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT); // <-- Automatically sanitized for SQL by PDO
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getBracketDivisionsByRound($bracketId, $roundId) {
		$stmt = $this->connection->prepare('SELECT * FROM VW_Matches WHERE matchid = :matchid and competitionroundid = :roundid ORDER BY TieBreakOrder');
		$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT); 
		$stmt->bindParam(':roundid', $roundId, PDO::PARAM_INT); 
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getBracketEntries($bracketId, $divisionId, $userId) {
		$stmt = $this->connection->prepare("SELECT * FROM BracketEntry WHERE MatchId = :matchid AND DivisionId = :divid AND UserId = :userid");
		$stmt->bindParam(':matchid',  $bracketId, PDO::PARAM_INT);
		$stmt->bindParam(':divid', $divisionId, PDO::PARAM_INT);
		$stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getBracketEntriesDict($bracketId, $divisionId, $userId) {
		$entries = $this->getBracketEntries($bracketId, $divisionId, $userId);
		$dict = [];
		foreach($entries as $en)
		{
			$dict[$en['Position']] = $en;
		}
		
		return $dict;
	}
	
	public function addBracketEntries($bracketEntries, $bracketId, $roundId, $divisionId, $userId) {	    
		// do a delete and insert
		$stmt = $this->connection->prepare("DELETE FROM BracketEntry WHERE MatchId = :matchid AND RoundId = :roundid AND DivisionId = :divid AND UserId = :userid");
		$stmt->bindParam(':matchid',  $bracketId, PDO::PARAM_INT);
		$stmt->bindParam(':roundid', $roundId, PDO::PARAM_INT);
		$stmt->bindParam(':divid', $divisionId, PDO::PARAM_INT);
		$stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
		$stmt->execute();
			
		foreach($bracketEntries as $br)
		{
			if($br->getTeamId() != null)
			{
				// pass by reference error from php if you don't do this first
				$position = $br->getPosition();
				$teamId = $br->getTeamId();
				$teamName = $br->getTeamName();
				
				$stmt = $this->connection->prepare('INSERT INTO BracketEntry (MatchId, RoundId, DivisionId, UserId, Position, TeamId, TeamName) VALUES (:matchid, :roundid, :divid, :userid, :pos, :teamid, :teamname)');
				$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT);
				$stmt->bindParam(':roundid', $roundId, PDO::PARAM_INT);
				$stmt->bindParam(':divid', $divisionId, PDO::PARAM_INT);
				$stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
				$stmt->bindParam(':pos', $position, PDO::PARAM_INT);
				$stmt->bindParam(':teamid', $teamId, PDO::PARAM_INT);
				$stmt->bindParam(':teamname', $teamName, PDO::PARAM_STR);
				$stmt->execute();
			}
		}
	}
	
	public function getWorldsPlacements($season, $roundId, $divisionId) {
		$stmt = $this->connection->prepare("SELECT * FROM WorldsWinners WHERE DivisionId = :divid AND CompetitionRoundId = :round AND Season = :season ORDER BY Position");
		$stmt->bindParam(':divid', $divisionId, PDO::PARAM_INT);
		$stmt->bindParam(':round', $roundId, PDO::PARAM_INT);
		$stmt->bindParam(':season', $season, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getWorldsPlacementsDict($season, $roundId, $divisionId) {
		$entries = $this->getWorldsPlacements($season, $roundId, $divisionId);
		$dict = [];
		foreach($entries as $en)
		{
			$dict[$en['Position']] = $en;
		}
		
		return $dict;
	}
	
	public function addWorldsPlacements($placements, $roundId, $divisionId, $season) 
	{	    
		// do a delete and insert
		$stmt = $this->connection->prepare("DELETE FROM WorldsWinners WHERE DivisionId = :divid AND CompetitionRoundId = :round Season = :season ORDER BY Position");
		$stmt->bindParam(':divid', $divisionId, PDO::PARAM_INT);
		$stmt->bindParam(':round', $roundId, PDO::PARAM_INT);
		$stmt->bindParam(':season', $season, PDO::PARAM_INT);
		$stmt->execute();
			
		foreach($placements as $pl) 
		{
			if(strlen($pl['TeamId']) > 0) 
			{
				$stmt = $this->connection->prepare('INSERT INTO WorldsWinners (Season, CompetitionRoundId, DivisionId, InsertBy, Position, TeamId, TeamName) VALUES (:season, :round, :divid, :userid, :pos, :teamid, :teamname)');
				$stmt->bindParam(':season', $pl['Season'], PDO::PARAM_INT);
				$stmt->bindParam(':round', $pl['RoundId'], PDO::PARAM_INT);
				$stmt->bindParam(':divid', $pl['DivisionId'], PDO::PARAM_INT);
				$stmt->bindParam(':userid', $pl['UserId'], PDO::PARAM_INT);
				$stmt->bindParam(':pos', $pl['Position'], PDO::PARAM_INT);
				$stmt->bindParam(':teamid', $pl['TeamId'], PDO::PARAM_INT);
				$stmt->bindParam(':teamname', $pl['TeamName'], PDO::PARAM_STR);
				$stmt->execute();
			}
		}
	}
	
}


?>