<?php
class BracketData
{
	protected $connection;
	
	function __construct($connection) {
		$this->connection = $connection;
	}

    public function getAllBrackets($season)
	{
		$stmt = $this->connection->prepare('SELECT * FROM BracketMatch WHERE season = :season');
		$stmt->bindParam(':season', $season, PDO::PARAM_INT); // <-- Automatically sanitized for SQL by PDO
		$stmt->execute();
		
		return $stmt;
	}
	
	public function getBracket($bracketId)
	{
		$stmt = $this->connection->prepare('SELECT * FROM BracketMatch WHERE matchid = :matchid');
		$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT); // <-- Automatically sanitized for SQL by PDO
		$stmt->execute();
		
		// return just one row
		return $stmt->fetch();
	}
	
	public function getBracketDivisions($bracketId)
	{
		$stmt = $this->connection->prepare('SELECT DivisionId, DivisionName FROM VW_Matches WHERE matchid = :matchid');
		$stmt->bindParam(':matchid', $bracketId, PDO::PARAM_INT); // <-- Automatically sanitized for SQL by PDO
		$stmt->execute();
		
		return $stmt;
	}
}


?>