<?php
/*
For tripecode information, please see: 
http://wakaba.c3.cx/sup/kareha.pl/1099727823/
*/
class Encryption
{
	static function tripecode($string)
	{
		return base64_encode(md5($string, true));
	}
	
	static function secureTripecode($string)
	{
		return self::tripecode($string.Config::get('salt'));
	}
}
?>