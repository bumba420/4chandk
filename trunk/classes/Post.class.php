<?php
class Post
{
	private $id				=	0;
	private $thread_id		= 	0;
	private $board_id		=	0;
	private $title			=	'';
	private $name			=	'';
	private $email			= 	'';
	private $password		=	'';
	private $message		= 	'';
	private $file			= 	null;
	private $posted_at		=	0;
	private $last_update	=	0;
	private $ip				= 	'';
	
	private $board			=	null;
	private $data			= 	false;
	
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
							 		$args[7],
							 		$args[8]
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

	private function getByArgs($id,
							   $thread_id, 
							   $board_id, 
							   $title, 
							   $name, 
							   $email, 
							   $message, 
							   $password, 
							   $file)
	{
		$this->id			=	$id;
		$this->thread_id	=	$thread_id;
		$this->board_id 	=	$board_id;
		$this->title 		=	$title;
		$this->name			=	$name;
		$this->email		=	$email;
		$this->message		=	$message;
		$this->password		=	$password;
		$this->posted_at	=	time();
		$this->last_update	=	$this->posted_at;
		$this->ip			=	$_SERVER['REMOTE_ADDR'];

		//die(var_dump($file));
		
		if (is_uploaded_file($file['tmp_name']))
		{
			//die("hmmm ".var_dump($file));
			$this->file		= 	new Image($file, $this->posted_at*100, 200, 200, 5000000, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF));
		}
		elseif (file_exists(Config::get('image_folder').'/'.$file))
		{
			$this->file		= 	new Image(null, $file, 200, 200, 5000000, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF));
		}

		$this->data			=	true;
	}
	
	function savePost()
	{
		//die("internet: ".$this->file);
		$thread_id = is_null($this->thread_id) ? "NULL" : $this->thread_id;
		
		$query = "INSERT INTO posts (thread_id,
									board_id,
									title, 
									name, 
									email, 
									password, 
									message, 
									filename, 
									ip, 
									posted_at,
									last_update
									) VALUES (
									".$thread_id.",
									".$this->board_id.",
									'".$this->title."', 
									'".$this->name."', 
									'".$this->email."', 
									'".$this->password."', 
									'".$this->message."', 
									'".$this->file->getFilename()."', 
									'".$_SERVER['REMOTE_ADDR']."', 
									".$this->posted_at.",
									".$this->last_update."
									)";
		
		if (Database::singleton()->query($query))
		{
			// This could/should? be done by a MySQL trigger
			if (!is_null($thread_id))
			{
				$query = "UPDATE posts SET last_update = ".$this->posted_at." 
						 WHERE id = ".$thread_id;
			
				Database::singleton()->query($query);			
			}
			return true;
		}
		else 
		{
			die("Error:".Database::singleton()->error.'<hr />'.$query);
		}
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