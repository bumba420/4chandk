<?php
class post
{
	var $title		=	'';
	var $name		=	'';
	var $email		= 	'';
	var $message	= 	'';
	var $file		= 	null;
	var $password	=	'';
	
	var $mysql		= 	null;
	
	function __constructor($connection)
	{
		$this->mysql = $connection;
		return;
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
		$stmt = $this->mysql->prepare("INSERT INTO (title, 
													name, 
													email, 
													password, 
													message, 
													filename, 
													ip, 
													posted_at
													) VALUES (
													?, ?, ?, ?, ?, ?, ?, ?");
		$stmt->bind_param("sssssssi", $title, $name, $email, $password, $message, $file, $_SERVER['REMOTE_ADDR'], microtime(true));
		return $stmt->execute();
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
	
}
?>