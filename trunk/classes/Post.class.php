<?php
class Post
{
	private $id				=	0;
	private $thread_id		= 	0;
	private $board_id		=	0;
	private $title			=	'';
	private $name			=	'';
	private $tripecode		=	'';
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
			return $this->getById(intval($args[0]));
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
							 		$args[8],
							 		$args[9],
							 		$args[10]
							 		);
		}
	}
	
	public function __toString() 
	{
		// Thanks to SandBender from #php @ freenode
		// http://us3.php.net/manual/en/language.oop5.magic.php#72532
		return(get_class($this)."@".$this->__uniqid);
	}
	
	private function getById($id)
	{
		$query = "SELECT * FROM ".Config::get('post_relation')." 
				  WHERE id = ".$id;

		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) {
				foreach ($row as $key => $value)
				{
					$this->$key	= stripslashes($value);
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
							   $tripecode,
							   $email, 
							   $message, 
							   $password, 
							   $posted_at,
							   $file)
	{
		$this->id			=	$id;
		$this->thread_id	=	$thread_id;
		$this->board_id 	=	$board_id;
		$this->title 		=	$title;
		$this->name			=	User::calcUsername($name);
		$this->tripecode	=	$tripecode;
		$this->email		=	$email;
		$this->message		=	$message;
		$this->password		=	$password;
		$this->posted_at	=	$posted_at;
		$this->last_update	=	$this->posted_at;
		$this->ip			=	$_SERVER['REMOTE_ADDR'];
		
		if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name']))
		{
			$this->file		= 	new Image($file, $this->posted_at*100, Config::get('image_max_width'), Config::get('image_max_height'), new Board($board_id), array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF));
		}
		elseif (is_file(Config::get('image_folder').'/'.$file))
		{
			$this->file		= 	new Image(null, $file, Config::get('image_max_width'), Config::get('image_max_height'), new Board($board_id), array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF));
		}

		$this->data			=	true;
	}
	
	function savePost()
	{
		if (!$this->validatePost())
		{
			die("failed to validate");
			return false;
		}
		
		$thread_id 	= is_null($this->thread_id) ? "NULL" : $this->thread_id;
		$filename	=	$this->file	? $this->file->getFilename() : '';
		
		$query = "INSERT INTO ".Config::get('post_relation')." (
									id,
									thread_id,
									board_id,
									title, 
									name,
									tripecode, 
									email, 
									password, 
									message, 
									filename, 
									ip, 
									posted_at,
									last_update
									)
									SELECT (MAX(id)+1),
										   	".$thread_id.",
											".intval($this->board_id).",
											'".mysql_real_escape_string($this->title)."', 
											'".mysql_real_escape_string($this->name)."', 
											'".mysql_real_escape_string($this->tripecode)."', 
											'".mysql_real_escape_string($this->email)."', 
											'".mysql_real_escape_string($this->password)."', 
											'".mysql_real_escape_string($this->message)."', 
											'".mysql_real_escape_string($filename)."', 
											'".$_SERVER['REMOTE_ADDR']."', 
											".$this->posted_at.",
											".$this->last_update."
									FROM ".Config::get('post_relation')."
									WHERE board_id = ".intval($this->board_id);
		//die($query);
		if (Database::singleton()->query($query))
		{
			// This could/should? be done by a MySQL trigger
			if (!is_null($thread_id) && strtolower($this->email) != strtolower(Config::get('dont_bump')))
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
	
	private function validatePost()
	{
		//$file_validation	=	is_null($this->file) ? true : $this->file->validate($this->board->filesize, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF));
		$file_validation	=	!isset($this->file) ? true : $this->file->validate($this->board->filesize, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF));
		
		if (is_null($this->thread_id)) 
		{
			return !empty($this->message) && !empty($this->file) && $file_validation;
		}
		else 
		{
			return !empty($this->message) || $file_validation;
		}
	}
	
	public static function deletePost()
	{
		$id = func_num_args() == 1 ? func_get_arg(0) : $this->id;
		return Database::singleton()->query("DELETE FROM posts WHERE id = ".$id);
	}
	
	function hasFile()
	{
		return !is_null($this->file);
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
	
	function getTripecode()
	{
		return $this->tripecode;
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
	
	function getDate()
	{
		return date(Config::get('date_format'), $this->posted_at);
	}
	
}
?>