<?php
class Post
{
	var $id			=	0;
	var $thread_id	= 	0;
	var $board_id	=	0;
	var $title		=	'';
	var $name		=	'';
	var $email		= 	'';
	var $message	= 	'';
	var $file		= 	null;
	var $password	=	'';
	var $ip			= 	'';
	
	var $board		=	null;
	
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
		
		//$this->mysql = $connection;
		//return;
	}
	
	private function getById($id)
	{
		$stmt = Database::prepare("SELECT * FROM posts WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($post);
		$stmt->fetch();
	
		return;
	}

	private function getByArgs($thread_id, $board_id, $title, $name, $email, $message, $password, $file)
	{
		$this->thread_id	= $thread_id;
		$this->board_id 	= $board_id;
		$this->title 		= $title;
		$this->name			= $name;
		$this->email		= $email;
		$this->message		= $message;
		$this->password		= $password;
		$this->ip			= $_SERVER['REMOTE_ADDR'];
	}
	
	function savePost()
	{
		return Database::query("INSERT INTO posts (thread_id,
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
		/*
		$stmt = $this->mysql->prepare("INSERT INTO posts (title, 
												name, 
											    email, 
												password, 
												message, 
												filename, 
												ip, 
												posted_at
												) VALUES (
												?, ?, ?, ?, ?, ?, ?, ?)");
		*/
		/*
		$stmt->bind_param("sssssssi", $this->title, 
									  $this->name, 
									  $this->email, 
									  $this->password, 
									  $this->message, 
									  $this->file, 
									  $_SERVER['REMOTE_ADDR'], 
									  microtime(true)
									  );
		*/
		/*$stmt->bind_param("sssssssi", $title, 
									  $name, 
									  $email, 
									  $password, 
									  $message, 
									  $file, 
									  $ip, 
									  $time
									  );
									  
		$title 		= '$this->title';
		$name		= '$this->name';
		$email		= '$this->email';
		$password	= '$this->password';
		$message	= '$this->message';
		$file		= '$this->file';
		$ip			= "lol";
		$time		= microtime(true);
		
		//return $stmt->execute();
		$stmt->execute();
		printf("Error: %s.\n", $stmt->error);*/
		//return false;
		
	}
	
	public static function deletePost()
	{
		/*
		$stmt = $this->mysql->prepare("DELETE posts WHERE id = ?");
		$stmt->bind_param("i", $this->id);
		return $stmt->execute();
		*/
		$id = func_num_args() == 1 ? func_get_arg(0) : $this->id;
		return Database::query("DELETE FROM posts WHERE id = ".$id);
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