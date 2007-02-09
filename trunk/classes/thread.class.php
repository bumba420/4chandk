<?php
class thread 
{
	var $thread_id			=	'';
	var $first_post			=	NULL;
	var $posts				= 	NULL; // does this take up unneccassry memory?
	
	var $mysql				= 	NULL;
	
	public function __construct($connection, $id)
	{
		$this->mysql = $connection;
		/*
		$stmt = $this->mysql->prepare("SELECT * FROM posts WHERE id = ? AND parent_id IS NULL");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($post);
		$stmt->fetch();
		*/
		//return;
	}
	
	public function posts()
	{
		if (is_null($this->posts))
		{
			$posts = array();
		
			$stmt = $this->mysql->prepare("SELECT * 
										   FROM posts 
										   WHERE theard_id = ? 
										   OR id = ? 
										   ORDER BY id ASC");
			$stmt->bind_param("ii", $this->thread_id, $this->thread_id);
			$stmt->execute();
			$stmt->bind_result($title, $name, $email, $message, $password, $file);
		
			while ($stmt->fetch())
			{
				$posts[] = new post($title, $name, $email, $message, $password, $file);
			}
		
			$this->first_post	= $posts[0];
			$this->posts 		= $posts;
		}
		
		return $this->posts;
	}
	
	function firstPost()
	{
		return;
	}
	
	function addPost($title, $name, $message, $image)
	{
		return;
	}
	
	function deletePost($id)
	{
		return;
	}
}
?>