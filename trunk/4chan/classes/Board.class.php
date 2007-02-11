<?php
class Board {
	
	var $id				=	0;
	var $name			=	'';
	var $description	= 	'';
	var $banner			=	'';
	
	var $section		=	NULL;
	
	function __construct() 
	{
		return;
	}
	
	function threads($amount = NULL, $offset = NULL)
	{
		$amount = $amount ? $amount : Config::get('post_pr_page');
		$offset = $offset ? $offset : 0;
		 
		$threads = array();
		
		$stmt = Database::prepare("SELECT id 
								   FROM posts 
								   WHERE board_id = ? 
								   AND thread_id IS NULL 
								   ORDER BY id ASC
								   LIMIT ".$amount.",".$offset);
		$stmt->bind_param("i", $this->id);
		$stmt->execute();
		$stmt->bind_result($id);
		
		while ($stmt->fetch())
		{
			$threads[] = new Thread($id);
		}
	
		return $threads;
	}
	
	function deleteBoard($id)
	{
		return Database::query("DELETE FROM boards WHERE id = ".$this->id);
	}
	
	function deleteThread($id)
	{
		return Database::query("DELETE FROM posts WHERE theard_id = ".$id." OR id = ".$id);
	}
}
?>