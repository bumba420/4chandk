<?php
/*
For tripecode information, please see: 
http://wakaba.c3.cx/sup/kareha.pl/1099727823/
*/
class Encryption
{
	static function tripecode($string)
	{
		$hash 	=	Config::get('tripecode_hash');
		return substr(base64_encode($hash($string)), 0, Config::get('tripecode_length'));
	}
	
	static function secureTripecode($string)
	{
		return self::tripecode($string.Config::get('salt'));
	}
}
?>