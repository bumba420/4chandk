<?php
/*
THIS IS NOT THE CONFIGURATION FILE!
DO NOT EDIT THIS UNLESS YOU *KNOW* WHAT YOU ARE DOING!
EDIT /config/config.php INSTEAD!
*/
class Config extends Singleton 
{
	static function initialize()
	{
		set_magic_quotes_runtime(0);
		if (get_magic_quotes_gpc()) 
		{
			$_GET 		=	array_map("stripslashes", $_GET);
			$_POST 		=	array_map("stripslashes", $_POST);
			$_REQUEST 	=	array_map("stripslashes", $_REQUEST);
			$_COOKIE 	=	array_map("stripslashes", $_COOKIE);
		}
	}
}
?>