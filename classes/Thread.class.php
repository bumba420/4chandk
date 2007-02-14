<?php
class Thread 
{
	var $thread_id			=	'';
	var $board_id			= 	'';
	var $first_post			=	NULL;
	var $posts				= 	NULL; // does this take up a lot of unneccassry memory?
	
	public function __construct($id)
	{
		$this->thread_id = $id;
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
		
			$stmt = Database::singleton()->prepare("SELECT * 
										   FROM ".Config::get('post_relation')." 
										   WHERE theard_id = ? 
										   OR id = ? 
										   ORDER BY id ASC");
			$stmt->bind_param("ii", $this->thread_id, $this->thread_id);
			$stmt->execute();
			$stmt->bind_result($title, $name, $email, $message, $password, $file);
		
			while ($stmt->fetch())
			{
				$posts[] = new Post($title, $name, $email, $message, $password, $file);
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
}
?>