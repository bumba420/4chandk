<?php
class Post
{
	private $id			=	0;
	private $thread_id	= 	0;
	private $board_id	=	0;
	private $title		=	'';
	private $name		=	'';
	private $email		= 	'';
	private $password	=	'';
	private $message	= 	'';
	private $file		= 	null;
	private $posted_at	=	0;
	private $ip			= 	'';
	
	private $board		=	null;
	private $data		= 	false;
	
	function __construct()
	{
		// I don't really like this contructor, 
		// but what the heck, 
		// I don't know a better solution
		$args = func_get_args();
				
		if (func_num_args() == 1)
		{
			return $this->getById($args[0]);
		}
		else 
		{
			return $this->getByArgs($args[0],
							 		$args[1],
							 		$args[2],
							 		$args[3],
							 		$args[4],
							 		$args[5],
							 		$args[6],
							 		$args[7]
							 		);
		}
	}
	
	private function getById($id)
	{
		$query = "SELECT * FROM ".Config::get('post_relation')." 
				  WHERE id = ".$id;

		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) {
				foreach ($row as $key => $value)
				{
					$this->$key	= $value;
				}
			}
		}
		
		$this->data	=	true;
	}

	private function getByArgs($thread_id, 
							   $board_id, 
							   $title, 
							   $name, 
							   $email, 
							   $message, 
							   $password, 
							   $file)
	{
		$this->thread_id	= $thread_id;
		$this->board_id 	= $board_id;
		$this->title 		= $title;
		$this->name			= $name;
		$this->email		= $email;
		$this->message		= $message;
		$this->password		= $password;
		$this->ip			= $_SERVER['REMOTE_ADDR'];
		
		$this->data	=	true;
	}
	
	function savePost()
	{
		return Database::singleton()->query("INSERT INTO posts (thread_id,
												board_id,
												title, 
												name, 
											    email, 
												password, 
												message, 
												filename, 
												ip, 
												posted_at
												) VALUES (
												".$this->thread_id.",
												".$this->board_id.",
												'".$this->title."', 
												'".$this->name."', 
												'".$this->email."', 
												'".$this->password."', 
												'".$this->message."', 
												'".$this->file."', 
												'".$_SERVER['REMOTE_ADDR']."', 
												".microtime(true)."
												)");
	}
	
	public static function deletePost()
	{
		$id = func_num_args() == 1 ? func_get_arg(0) : $this->id;
		return Database::singleton()->query("DELETE FROM posts WHERE id = ".$id);
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function getThreadId()
	{
		return $this->thread_id;
	}
	
	function getBoradId()
	{
		return $this->board_id;
	}
	
	function getTitle()
	{
		return $this->title;
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function getEmail()
	{
		return $this->email;
	}
	
	function getPassword()
	{
		return $this->password;
	}
	
	function getMessage()
	{
		return $this->message;
	}
	
	function getFile()
	{
		return $this->file;
	}
	
	function getIp()
	{
		return $this->ip;
	}
	
}
?>