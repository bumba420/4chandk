<?php
class post
{
	var $title		=	'';
	var $name		=	'';
	var $email		= 	'';
	var $message	= 	'';
	var $file		= 	null;
	var $password	=	'';
	var $ip			= 	'';
	
	var $board		=	null;
	var $mysql		= 	null;
	
	function __construct()
	{
		// I don't really like this contructor, 
		// but what the heck, 
		// I don't know a better solution
		$args = func_get_args();
		$this->mysql = $args[0];
		
		if (func_num_args() == 2)
		{
			return $this->getById($args[1]);
		}
		else 
		{
			return $this->getByArgs($args[1],
							 		$args[2],
							 		$args[3],
							 		$args[4],
							 		$args[5],
							 		$args[6]
							 		);
		}
		
		//$this->mysql = $connection;
		//return;
	}
	
	function getById($id)
	{
		$stmt = $this->mysql->prepare("SELECT * FROM posts WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($post);
		$stmt->fetch();
	
		return;
	}

	function getByArgs($title, $name, $email, $message, $password, $file)
	{
		$this->title 	= $title;
		$this->name		= $name;
		$this->email	= $email;
		$this->message	= $message;
		$this->password	= $password;
		$this->ip		= $_SERVER['REMOTE_ADDR'];
	}
	
	function savePost()
	{
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
		$stmt->bind_param("sssssssi", $title, 
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
		//$stmt->execute();
		//printf("Error: %s.\n", $stmt->error);
		return false;
	}
	
	function detelePost($id)
	{
		$stmt = $this->mysql->prepare("DELETE posts WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
	}
	
	function deletePosts($ids)
	{
		foreach ($ids as $id)
		{
			$this->detelePost($id);
		}
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