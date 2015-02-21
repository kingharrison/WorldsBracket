<?php
class BracketEntry
{
	private $bracketId;
	private $userId;
	private $divisionId;
	private $position;
	private $teamId;
	private $teamName;
	
	public function getBracketId()
	{
		return $this->bracketId;
	}
	
	public function setBracketId($bracketId)
	{
		$this->bracketId = $bracketId;
	}
	
	public function getUserId()
	{
		return $this->userId;
	}
	
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	public function getDivisionId()
	{
		return $this->divisionId;
	}
	
	public function setDivisionId($divisionId)
	{
		$this->divisionId = $divisionId;
	}
	
	public function getPosition()
	{
		return $this->position;
	}
	
	public function setPosition($position)
	{
		$this->position = $position;
	}
	
	public function getTeamId()
	{
		return $this->teamId;
	}
	
	public function setTeamId($teamId)
	{
		$this->teamId = $teamId;
	}
	
	public function getTeamName()
	{
		return $this->teamName;
	}
	
	public function setTeamName($teamName)
	{
		$this->teamName = $teamName;
	}
	
	
	
}
?>