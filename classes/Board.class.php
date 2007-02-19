<?php
class Board {
	
	private  $id				=	0;
	private  $name				=	'';
	private  $description		= 	'';
	private  $dir				=	'';
	private  $filesize			=	0;
	private  $banner			=	'';
	
	private  $threads 			= 	array();
	private  $section_id		=	NULL;
	private	 $section_name		=	'';
	private  $post_amount		=	NULL;
	
	private  $data				=	false;
	
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
					WHERE board_id = ".$this->id." 
					AND thread_id IS NULL 
					ORDER BY last_update DESC
					LIMIT ".$offset.",".$amount;
		

		if ($stmt = Database::singleton()->prepare($query)) 
		{
			$stmt->execute();
			$stmt->bind_result($id);
		
			while ($stmt->fetch())
			{
				$this->threads[] = new Thread($id);
			}
		}
	
		return $this->threads;
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
		$query	=	"SELECT board.id,
							board.section_id, 
							board.name, 
							board.dir, 
							board.description, 
							board.filesize,
							board.banner,
							section.name AS section_name,
							COUNT(*) AS post_amount
					FROM ".Config::get('board_relation')." AS board,
					".Config::get('section_relation')." AS section,
					".Config::get('post_relation')." AS post
					WHERE board.id = ".$this->id." 
					AND board.id = post.board_id 
					AND post.thread_id IS NULL
					AND board.section_id = section.id 
					GROUP BY section_id
					LIMIT 0 , ".Config::get('threads_pr_board');

		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) {
				foreach ($row as $key => $value)
				{
					$this->$key	= $value;
				}
			}
	  
			$this->data		= true;
		}
		else 
		{
			die("Error:".Database::singleton()->error.'<hr />'.$query);
		}
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function getName()
	{
		if (!$this->data) {	$this->getData(); }
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
		return $this->dir;
	}
	
	function getBanner()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->banner;
	}
	
	function getSectionName()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->section_name;
	}
	
	function getFilesizeInKB()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->filesize / 1024;
	}
	
	function getPostAmount()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->post_amount;
	}
	
	function getURL()
	{
		return URL::board($this->id);
	}
}
?>