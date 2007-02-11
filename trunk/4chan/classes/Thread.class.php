<?php
class Thread 
{
	var $thread_id			=	'';
	var $board_id			= 	'';
	var $first_post			=	NULL;
	var $posts				= 	NULL; // does this take up unneccassry memory?
	
	public function __construct($id)
	{
		$this->thread_id = $id;
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
		
			$stmt = Database::prepare("SELECT * 
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
		if ($this->first_post)
		{
			return $this->first_post;
		}
		else 
		{
			// This can be reduced to a single query
			$result = Database::query("SELECT id FROM posts WHERE thread_id = ".$this->id." AND parent_id IS NULL");
			die("Help me - I'm only a stub");
		}
	}
	
	function addPost($title, $name, $email, $message, $password, $file)
	{
		$post = new Post($this->id, $this->board_id, $title, $name, $email, $message, $password, $file);
		if ($post->savePost())
		{
			return $post;
		}
		else 
		{
			return false;
		}
	}
	
	function deletePost($id)
	{
		return Post::deletePost($id);
	}
}
?>