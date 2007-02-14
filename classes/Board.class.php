<?php
class Board {
	
	private  $id				=	0;
	private  $name				=	'';
	private  $description		= 	'';
	private  $directory			=	'';
	private  $banner			=	'';
	
	private  $threads 			= 	array();
	private  $section_id		=	NULL;
	
	private  $data				= false;
	
	function __construct($id) 
	{
		$this->id = $id;
	}
	
	function threads($amount = NULL, $offset = NULL)
	{
		$amount = $amount ? $amount : Config::get('post_pr_page');
		$offset = $offset ? $offset : 0;
		 
		$query	=	"SELECT id 
					FROM ".Config::get('post_relation')." 
					WHERE board_id = ? 
					AND thread_id IS NULL 
					ORDER BY id ASC
					LIMIT ".$amount.",".$offset;
		
		if ($stmt = Database::singleton()->prepare($query)) {
			$stmt->bind_param("i", $this->id);
			$stmt->execute();
			$stmt->bind_result($id);
		
			while ($stmt->fetch())
			{
				$threads[] = new Thread($id);
			}
		}
	
		return $threads;
	}
	
	function deleteBoard($id)
	{
		return Database::singleton()->query("DELETE FROM ".Config::get('board_relation')." WHERE id = ".$this->id);
	}
	
	function deleteThread($id)
	{
		return Database::singleton()->query("DELETE FROM ".Config::get('post_relation')." WHERE theard_id = ".$id." OR id = ".$id);
	}
	
	private function getData()
	{
		$query	=	"SELECT section_id, name, dir, description, banner 
					FROM ".Config::get('board_relation')."
					WHERE id = ".$this->id;

		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) {
				foreach ($row as $key => $value)
				{
					$this->$key	= $value;
				}
			}
	  
			$this->data		= true;
		}
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function getName()
	{
		if (!$this->data) {	$this->getData(); }
		//die("lol".$this->name);
		return $this->name;
	}
	
	function getDescription()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->description;
	}
	
	function getDirectory()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->directory;
	}
}
?>