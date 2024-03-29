<?php
class Thread 
{
	private  $thread_id			=	'';
	private  $board_id			= 	'';
	private  $first_post		=	NULL;
	private  $posts				= 	NULL; // does this take up a lot of unneccassry memory?
	
	public function __construct($thread_id, $board_id)
	{
		$this->thread_id 	=	$thread_id;
		$this->board_id		=	$board_id;
	}
	
	private function getData()
	{
		return;
	}
	
	public function posts()
	{
		if (is_null($this->posts))
		{
			$posts = array();
			$query = "SELECT id, board_id, title, name, tripecode, email, message, password, posted_at, filename 
					  FROM ".Config::get('post_relation')." 
					  WHERE 
					  (
					  	thread_id = ".$this->thread_id." 
					  	OR id = ".$this->thread_id."
					  )
					  AND board_id = ".$this->board_id."
					  ORDER BY id ASC";

			$stmt = Database::singleton()->prepare($query);
			$stmt->execute();
			$stmt->bind_result($id, $board_id, $title, $name, $tripecode, $email, $message, $password, $posted_at, $file);
		
			while ($stmt->fetch())
			{
				$posts[] = new Post($id, 
									$this->thread_id, 
									$board_id, 
									stripslashes($title), 
									stripslashes($name), 
									stripslashes($tripecode),
									stripslashes($email), 
									stripslashes($message), 
									stripslashes($password), 
									$posted_at,
									stripslashes($file));
			}
		
			$this->first_post	= $posts[0];
			$this->posts 		= $posts;
		}
		
		return $this->posts;
	}
	
	public function firstPost()
	{
		if ($this->first_post)
		{
			return $this->first_post;
		}
		else 
		{
			$query = "SELECT id FROM ".Config::get('post_relation')." 
					WHERE thread_id = ".$this->id." 
					AND parent_id IS NULL";
			
			if ($result = Database::singleton()->query($query))
			{
				while ($row = $result->fetch_assoc()) {
					$this->first_post = new Post($row['id']);
				}
			}
		}
	}
	/*
	public function addPost($title, $name, $email, $message, $password, $file)
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
	*/
	public function deletePost($id)
	{
		return Post::deletePost($id);
	}
	
	public function getId()
	{
		return $this->thread_id;
	}
	
	function getReplyURL()
	{
		// FIXME
		return '?p=board&id='.$_GET['id'].'&thread_id='.$this->thread_id;
	}
}
?>