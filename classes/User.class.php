<?php
class User
{
	static function calcTripecode($input_name)
	{
		if (empty($input_name))
		{
			return '';
		}
		
		list($name, $secret)	=	explode("#", $input_name, 2);
		$tripecode = empty($secret) ? '' : Encryption::secureTripecode($secret);
		
		return $tripecode;
	}
	
	static function calcUsername($input_name)
	{
		if (empty($input_name))
		{
			return Config::get('blank_name');
		}
		
		list($name, $secret)	=	explode("#", $input_name, 2);
		$tripecode = empty($secret) ? '' : Encryption::secureTripecode($secret);
		
		return $name;
	}
}
?>