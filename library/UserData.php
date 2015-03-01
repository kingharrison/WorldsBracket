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