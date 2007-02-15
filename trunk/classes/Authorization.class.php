<?php
class Authorization
{
	static function isBanned(Board $board)
	{
		$query	=	"SELECT ip, expire 
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
				return true;
			}
			$result->close();
		}
		
		return false;
	}
}
?>