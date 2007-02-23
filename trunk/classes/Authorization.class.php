<?php
class Authorization
{
	static function isBanned(Board $board = null)
	{	
		$query	=	"SELECT ip, expire 
					FROM ".Config::get('ban_relation')."
					WHERE ip = '".$_SERVER['REMOTE_ADDR']."' 
					AND expire > ".time();
		
		if (isset($board))
		{
			$query	.=	" AND board_id = ".$board->getId();
		}
		
		if ($result = Database::singleton()->query($query))
		{
			if ($result->num_rows >= 1)
			{
				$result->close();
				die("You are banned. (note to self: fix this in the next release)");
				return true;
			}
			$result->close();
		}
		
		return false;
	}
}
?>