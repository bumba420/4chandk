<?php
class Cache
{
	public static function getCache($id)
	{
		return unserialize(file_get_contents(Config::get('cache_folder').'/'.$id));
	}
	
	public static function setCache($id, $data)
	{
		return file_put_contents(Config::get('cache_folder').'/'.$id, serialize($data));
	}
	
	public static function sweepCache($id)
	{
		return unlink(Config::get('cache_folder').'/'.$id);
	}
	
	public static function sweepWholeCache()
	{
		$return_value	=	true;
		
		if ($handle = opendir(Config::get('cache_folder')))
		{
			while (($file = readdir($handle)) !== false) 
			{
				if ($file[0] != ".")
				{
					if (!unlink(Config::get('cache_folder').'/'.$file))
					{
						$return_value	= false;
					}
				}
			}
			closedir($handle);
		}
		
		return $return_value;
	}
	
}
?>