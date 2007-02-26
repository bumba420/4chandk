<?php
class Board {
	
	private  $id				=	0;
	private  $name				=	'';
	private  $description		= 	'';
	private  $dir				=	'';
	private  $filesize			=	0;
	private  $banner			=	'';
	private  $threads_page		=	0;
	private  $threads_board		=	0;
	private  $forced_anonymous	=	false;
	private  $comment_length	=	0;
	private  $thread_length		=	0;
	
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
				$this->threads[] = new Thread($id, $this->id);
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
	
	function setData()
	{
		$this->data		=	true;
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
							board.threads_page,
							board.threads_board,
							board.forced_anonymous,
							board.comment_length,
							board.thread_length,
							section.id AS section_id,
							section.name AS section_name,
							(
								SELECT COUNT(*) 
								FROM ".Config::get('post_relation')."
								WHERE board_id = ".$this->id."
								AND thread_id IS NULL
							) AS post_amount
					FROM ".Config::get('board_relation')." AS board,
					".Config::get('section_relation')." AS section
					WHERE board.id = ".$this->id." 
					AND board.section_id = section.id
					LIMIT 0 , ".Config::get('threads_pr_board');
		//die($query);
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
	
	function getSectionId()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->section_id;
	}
	
	function getSectionName()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->section_name;
	}
	
	function getThreadsPrPage()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->threads_page;
	}
	
	function getThreadsPrBoard()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->threads_board;
	}
	
	function getForcedAnonymous()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->forced_anonymous;
	}
	
	function getCommentLength()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->comment_length;
	}
	
	function getThreadLength()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->thread_length;
	}
	
	function getFilesizeInKB()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->filesize / 1024;
	}
	
	function getFilesizeInB()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->filesize;
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